<?php

namespace Modules\Member\Http\Controllers;

use App\Models\Address;
use App\Models\AddressType;
use App\Models\Document;
use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Member\Http\Requests\StoreMemberRequest;
use Modules\Member\Http\Requests\UpdateMemberRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{
    public function __construct(protected MemberRepositoryInterface $memberRepo) {}

    // ── List ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'referral_code']);
        $members = $this->memberRepo->paginate($filters, 15);

        return view('member::members.index', compact('members', 'filters'));
    }

    // ── Detail ────────────────────────────────────────────────────────────
    public function show(Member $member)
    {
        $member->load([
            'referrer',
            'referrals',
            'addresses.addressType',
            'addresses.documents',
            'documents',
            'rewardAchievers.promotion',
        ]);

        $referralTree = $member->getReferralTree();

        return view('member::members.show', compact('member', 'referralTree'));
    }

    // ── Create ────────────────────────────────────────────────────────────
    public function create()
    {
        $addressTypes = AddressType::active()->get();
        return view('member::members.create', compact('addressTypes'));
    }

    // ── Store ─────────────────────────────────────────────────────────────
    public function store(StoreMemberRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Resolve referrer
            $referredBy = null;
            if ($request->filled('referrer_code')) {
                $referrer   = $this->memberRepo->findByReferralCode($request->referrer_code);
                $referredBy = $referrer?->id;
            }

            // Create member
            $member = $this->memberRepo->create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender'        => $request->gender,
                'nationality'   => $request->nationality,
                'ic_number'     => $request->ic_number,
                'referred_by'   => $referredBy,
                'status'        => $request->status ?? 'pending',
            ]);

            // Upload profile image
            if ($request->hasFile('profile_image')) {
                $this->storeDocument(
                    $request->file('profile_image'),
                    $member,
                    'profile_image',
                    "members/{$member->id}/profile"
                );
            }

            // Create addresses
            foreach ($request->input('addresses', []) as $index => $addressData) {
                $address = Address::create([
                    'member_id'       => $member->id,
                    'address_type_id' => $addressData['address_type_id'],
                    'address_line_1'  => $addressData['address_line_1'],
                    'address_line_2'  => $addressData['address_line_2'] ?? null,
                    'city'            => $addressData['city'],
                    'state'           => $addressData['state'],
                    'postcode'        => $addressData['postcode'],
                    'country'         => $addressData['country'] ?? 'Malaysia',
                ]);

                // Upload proof of address
                $uploadedFiles = $request->file('addresses') ?? [];
                if (isset($uploadedFiles[$index]['proof_of_address'])) {
                    $this->storeDocument(
                        $uploadedFiles[$index]['proof_of_address'],
                        $address,
                        'proof_of_address',
                        "addresses/{$address->id}/proof"
                    );
                }
            }

            DB::commit();

            return redirect()
                ->route('members.show', $member)
                ->with('success', "Member {$member->full_name} registered successfully. Referral code: {$member->referral_code}");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    // ── Edit ──────────────────────────────────────────────────────────────
    public function edit(Member $member)
    {
        $member->load(['addresses.addressType', 'addresses.documents', 'documents']);
        $addressTypes = AddressType::active()->get();

        return view('member::members.edit', compact('member', 'addressTypes'));
    }

    // ── Update ────────────────────────────────────────────────────────────
    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $this->memberRepo->update($member->id, [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender'        => $request->gender,
                'nationality'   => $request->nationality,
                'ic_number'     => $request->ic_number,
                'status'        => $request->status ?? $member->status,
            ]);

            // Re-upload profile image
            if ($request->hasFile('profile_image')) {
                $member->documents()->where('type', 'profile_image')->delete();
                $this->storeDocument(
                    $request->file('profile_image'),
                    $member,
                    'profile_image',
                    "members/{$member->id}/profile"
                );
            }

            // Delete flagged addresses
            foreach ($request->input('delete_addresses', []) as $addressId) {
                $address = Address::where('member_id', $member->id)->find($addressId);
                if ($address) {
                    Storage::disk('public')->deleteDirectory("addresses/{$address->id}");
                    $address->documents()->delete();
                    $address->delete();
                }
            }

            // Upsert addresses
            $uploadedFiles = $request->file('addresses') ?? [];
            foreach ($request->input('addresses', []) as $index => $addressData) {
                $address = isset($addressData['id'])
                    ? Address::where('member_id', $member->id)->find($addressData['id'])
                    : null;

                $fields = [
                    'member_id'       => $member->id,
                    'address_type_id' => $addressData['address_type_id'],
                    'address_line_1'  => $addressData['address_line_1'],
                    'address_line_2'  => $addressData['address_line_2'] ?? null,
                    'city'            => $addressData['city'],
                    'state'           => $addressData['state'],
                    'postcode'        => $addressData['postcode'],
                    'country'         => $addressData['country'] ?? 'Malaysia',
                ];

                if ($address) {
                    $address->update($fields);
                } else {
                    $address = Address::create($fields);
                }

                // Re-upload proof of address
                if (isset($uploadedFiles[$index]['proof_of_address'])) {
                    $address->documents()->where('type', 'proof_of_address')->delete();
                    $this->storeDocument(
                        $uploadedFiles[$index]['proof_of_address'],
                        $address,
                        'proof_of_address',
                        "addresses/{$address->id}/proof"
                    );
                }
            }

            DB::commit();

            return redirect()
                ->route('members.show', $member)
                ->with('success', 'Member updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    // ── Destroy ───────────────────────────────────────────────────────────
    public function destroy(Member $member): RedirectResponse
    {
        $member->load(['addresses.documents', 'documents']);

        foreach ($member->addresses as $address) {
            Storage::disk('public')->deleteDirectory("addresses/{$address->id}");
            $address->documents()->delete();
            $address->delete();
        }

        Storage::disk('public')->deleteDirectory("members/{$member->id}");
        $member->documents()->delete();
        $member->forceDelete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    // ── Export CSV ────────────────────────────────────────────────────────
    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $request->only(['search', 'status', 'referral_code']);
        $members = $this->memberRepo->all($filters);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="members_' . now()->format('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($members) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'First Name', 'Last Name', 'Email', 'Phone',
                'Gender', 'Date of Birth', 'Nationality', 'IC Number',
                'Status', 'Referral Code', 'Referred By', 'Registered At',
            ]);

            foreach ($members as $m) {
                fputcsv($handle, [
                    $m->id,
                    $m->first_name,
                    $m->last_name,
                    $m->email,
                    $m->phone,
                    $m->gender,
                    $m->date_of_birth?->format('Y-m-d'),
                    $m->nationality,
                    $m->ic_number,
                    $m->status,
                    $m->referral_code,
                    $m->referrer?->full_name,
                    $m->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    // ── Private Helpers ───────────────────────────────────────────────────
    private function storeDocument($file, $documentable, string $type, string $directory): Document
    {
        $fileName = $type . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($directory, $fileName, 'public');

        return Document::create([
            'documentable_type' => get_class($documentable),
            'documentable_id'   => $documentable->id,
            'type'              => $type,
            'original_name'     => $file->getClientOriginalName(),
            'file_name'         => $fileName,
            'file_path'         => $filePath,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
        ]);
    }
}
