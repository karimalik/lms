<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Org\Entities\OrgBranch;
use Modules\OrgInstructorPolicy\Entities\OrgPolicyBranch;

class ShowBranch extends Component
{
    protected $branch, $branchCode;
    public $codes = [];

    protected $listeners = ['checkOrgChart'];

    public function render()
    {
        if (Auth::check() && Auth::user()->role_id == 1) {
            $branches = OrgBranch::orderBy('order', 'asc')->get();
        } else {
            $policy_id = Auth::user()->policy_id;
            $assign = OrgPolicyBranch::where('policy_id', $policy_id)->pluck('branch_id')->toArray();
            $branches = OrgBranch::whereIn('id', $assign)->orderBy('order', 'asc')->get();
            foreach ($branches as $branch) {
                if (count($branches->where('id', $branch->parent_id)) == 0) {
                    $branch->parent_id = 0;
                }
            }
        }
        return view('livewire.show-branch', [
            'branches' => $branches
        ]);
    }


    public function branchFilter($branchCode)
    {
        $this->emit('addBranchFilter', $branchCode);
    }

    public function checkOrgChart($codes)
    {
        $this->codes = $codes;
    }
}
