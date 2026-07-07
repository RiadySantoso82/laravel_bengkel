<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') abort(403);
            return $next($request);
        });
    }

    public function index()
    {
        $data = ChecklistItem::latest()->get();
        return view('checklist_item.index', compact('data'));
    }

    public function create()
    {
        return view('checklist_item.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'description' => 'nullable', 'is_active' => 'boolean']);
        ChecklistItem::create($request->only(['name', 'description', 'is_active']));
        return redirect()->route('checklist-items.index')->with('success', 'Checklist item berhasil ditambahkan.');
    }

    public function edit(ChecklistItem $checklistItem)
    {
        return view('checklist_item.form', compact('checklistItem'));
    }

    public function update(Request $request, ChecklistItem $checklistItem)
    {
        $request->validate(['name' => 'required|max:255', 'description' => 'nullable', 'is_active' => 'boolean']);
        $checklistItem->update($request->only(['name', 'description', 'is_active']));
        return redirect()->route('checklist-items.index')->with('success', 'Checklist item berhasil diubah.');
    }

    public function destroy(ChecklistItem $checklistItem)
    {
        $checklistItem->delete();
        return redirect()->route('checklist-items.index')->with('success', 'Checklist item berhasil dihapus.');
    }

    public function demo(Request $request)
    {
        $count = (int) $request->input('count', 5);
        $items = [
            'Cek kondisi oli mesin',
            'Cek tekanan & kondisi ban',
            'Cek kampas rem depan & belakang',
            'Cek aki & kelistrikan',
            'Cek rantai & gir set',
            'Cek lampu & sein',
            'Cek sistem pendingin (radiator/coolant)',
            'Cek filter udara',
            'Cek kondisi shockbreaker',
            'Cek sistem kemudi & steering',
            'Cek kabel gas & kopling',
            'Cek mur & baut rangka',
            'Cek kondisi V-belt / CVT',
            'Cek busi & koil pengapian',
            'Cek kebocoran oli mesin',
        ];

        $data = [];
        for ($i = 0; $i < min($count, count($items)); $i++) {
            $data[] = ['name' => $items[$i], 'description' => null, 'is_active' => true];
        }
        ChecklistItem::insert($data);
        return redirect()->route('checklist-items.index')->with('success', count($data) . ' data checklist demo berhasil ditambahkan.');
    }
}
