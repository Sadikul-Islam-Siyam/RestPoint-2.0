<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{
    /**
     * Show the moderation dashboard listing pending reports.
     */
    public function index()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'moderator') {
            abort(403, 'Unauthorized moderation access.');
        }

        $reports = Report::where('status', 'pending')
            ->with(['user', 'reportable'])
            ->latest()
            ->paginate(15);

        return view('moderation.index', compact('reports'));
    }

    /**
     * Submit a report flag via AJAX.
     */
    public function flagAjax(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|string|in:post,comment',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|max:1000',
        ]);

        $type = $request->input('reportable_type');
        $id = $request->input('reportable_id');
        $modelClass = $type === 'post' ? Post::class : Comment::class;

        // Ensure target exists
        $modelClass::findOrFail($id);

        Report::create([
            'user_id' => auth()->id(),
            'reportable_type' => $modelClass,
            'reportable_id' => $id,
            'reason' => $request->input('reason'),
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you. Content reported successfully.'
        ]);
    }

    /**
     * Dismiss a flagged report (no action taken).
     */
    public function dismiss(Report $report)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'moderator') {
            abort(403);
        }

        $report->status = 'dismissed';
        $report->save();

        return redirect()->back()->with('success', 'Report dismissed successfully.');
    }

    /**
     * Resolve a flagged report (deletes the flagged post/comment).
     */
    public function resolve(Report $report)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'moderator') {
            abort(403);
        }

        DB::transaction(function () use ($report) {
            $report->status = 'resolved';
            $report->save();

            // Soft-delete or force-delete the reported post/comment
            if ($report->reportable) {
                $report->reportable->delete();
            }
        });

        return redirect()->back()->with('success', 'Offending content removed and report marked resolved.');
    }
}
