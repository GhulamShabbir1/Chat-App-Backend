<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkspaceInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $workspace;
    public $user;

    public function __construct(Workspace $workspace, User $user)
{
        $this->workspace = $workspace;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
     subject: 'Workspace Invitation - ' . $this->workspace->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.workspace-invitation',
        );
    }

    public function attachments(): array
    {
   return [];
    }
}

