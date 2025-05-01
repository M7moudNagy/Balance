<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipResource extends JsonResource
{
/**
* تحويل البيانات إلى شكل JSON
*/
public function toArray(Request $request): array
{
return [
'id'            => $this->id,
'title'         => $this->title,
'description'   => $this->description,
'published_date'=> $this->published_date,
'notes'         => $this->notes,
'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
'doctor_id'     => $this->doctor_id,
'category'      => new CategoryResource($this->whenLoaded('category')),
'patients'      => PatientResource::collection($this->whenLoaded('patients')),
//'updated_at'    => $this->updated_at->format('Y-m-d H:i:s'),
];
}
}
