<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateGroup extends Component
{
    public $name = '';

    public $description = '';

    public $maxMembers = 20;

    public $isPublic = false;

    public $showSuccessMessage = false;

    public $createdGroup = null;

    protected $rules = [
        'name' => 'required|string|min:3|max:50',
        'description' => 'nullable|string|max:255',
        'maxMembers' => 'required|integer|min:2|max:50',
        'isPublic' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Group name is required.',
        'name.min' => 'Group name must be at least 3 characters.',
        'name.max' => 'Group name cannot exceed 50 characters.',
        'maxMembers.min' => 'A group must have at least 2 members.',
        'maxMembers.max' => 'Maximum 50 members allowed per group.',
    ];

    public function createGroup()
    {

        $this->validate();

        try {
            // Small delay to show loading state (remove in production if you want)
            sleep(3); // 1 second delay - adjust as needed

            // Create the group
            $group = Group::create([
                'name' => $this->name,
                'description' => $this->description,
                'invite_code' => $this->generateUniqueInviteCode(),
                'owner_id' => auth()->id(),
                'max_members' => $this->maxMembers,
                'is_public' => $this->isPublic,
                'is_active' => true,
            ]);

            // Add the creator as the first member (admin)
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'joined_at' => now(),
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Store created group for success message
            $this->createdGroup = $group;
            $this->showSuccessMessage = true;

            // Reset form
            $this->reset(['name', 'description', 'maxMembers', 'isPublic']);

            // Emit event to refresh parent components
            $this->dispatch('groupCreated', $group->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create group. Please try again.');
        }
    }

    public function dismissSuccess()
    {
        $this->showSuccessMessage = false;
        $this->createdGroup = null;
    }

    private function generateUniqueInviteCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Group::where('invite_code', $code)->exists());

        return $code;
    }

    public function render()
    {
        return view('livewire.groups.create-group');
    }
}
