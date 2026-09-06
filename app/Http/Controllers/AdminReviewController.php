<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with('product')
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'statusFilter' => $request->status,
            'statuses' => Review::STATUS_NAMES,
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'status' => 'required|integer|in:' . implode(',', array_keys(Review::STATUS_NAMES)),
        ]);

        $review->update(['status' => $request->status]);

        return back()->with('message', 'Статус відгуку оновлено');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('message', 'Відгук видалено');
    }
}
