<?php
namespace App\Http\Controllers;

use App\Http\Resources\TipResource;
use App\Models\Patient;
use App\Models\PatientTask;
use App\Models\PatientTip;
use App\Models\Task;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
public function index()
{
$tips = Tip::all();
return TipResource::collection($tips);
}

public function store(Request $request)
{
$request->validate([
'title'         => 'required|string|max:255',
'description'   => 'nullable|string',
'category_id'   => 'required|exists:categories,id',
'published_date'=> 'nullable|date',
'notes'         => 'nullable|string',
'patients'      => 'required|array',
'patients.*'    => 'exists:patients,id',
'doctor_id'     => 'required|exists:doctors,id',
]);

$tip = Tip::create($request->all());
$tip->patients()->attach($request->patients);

return new TipResource($tip);
}

public function show($id)
{
$tip = Tip::with(['category', 'patients'])->find($id);
if (!$tip) {
return response()->json(['message' => 'Tip not found'], 404);
}
return new TipResource($tip);
}

public function update(Request $request, $id)
{
$tip = Tip::findOrFail($id);

$request->validate([
'title'         => 'sometimes|required|string|max:255',
'description'   => 'nullable|string',
'category_id'   => 'sometimes|required|exists:categories,id',
'published_date'=> 'nullable|date',
'notes'         => 'nullable|string',
'patients'      => 'array',
'patients.*'    => 'exists:patients,id',
]);

$tip->update($request->all());

if ($request->has('patients')) {
$tip->patients()->sync($request->patients);
}

return new TipResource($tip);
}

public function destroy($id)
{
$tip = Tip::find($id);
if (!$tip) {
return response()->json(['message' => 'Tip not found'], 404);
}
$tip->delete();
return response()->json(['message' => 'Tip deleted successfully']);
}

}
