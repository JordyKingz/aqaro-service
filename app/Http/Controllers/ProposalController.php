<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProposalRequest;
use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function getAll($page = 1, $limit = 10) {
        $proposals = Proposal::with('likes', 'dislikes')->paginate($limit, ['*'], 'page', $page);
        return response()->json([
            'proposals' => $proposals
        ], 200);
    }

    public function getById($id) {
        $proposal = Proposal::with('likes', 'dislikes')->find($id);
        if (!$proposal) {
            return response()->json([
                'message' => 'Proposal not found'
            ], 404);
        }
        return response()->json([
            'proposal' => $proposal
        ], 200);
    }

    public function create(CreateProposalRequest $request) {
        $user = $request->user();

        $validatedData = $request->validated();

        $proposal = new Proposal([
            'content' => $validatedData['content']
        ]);
        // Save the proposal to the database
        $proposal->save();

        return response()->json([
            'proposal' => $proposal,
            'message' => 'Successfully created proposal!'
        ], 201);
    }

    public function like(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = $request->user();

        if ($proposal->likes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Proposal already liked'], 400);
        }

        if ($proposal->dislikes()->where('user_id', $user->id)->exists()) {
            $proposal->dislikes()->where('user_id', $user->id)->delete();
        }

        $proposal->likes()->create(['user_id' => $user->id]);

        return response()->json(['message' => 'Proposal liked']);
    }

    public function dislike(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = $request->user();

        if ($proposal->dislikes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Proposal already disliked'], 400);
        }

        if ($proposal->likes()->where('user_id', $user->id)->exists()) {
            $proposal->likes()->where('user_id', $user->id)->delete();
        }

        $proposal->dislikes()->create(['user_id' => $user->id]);

        return response()->json(['message' => 'Proposal disliked']);
    }
}
