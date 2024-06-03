<?php

namespace App\Http\Controllers;

use App\Models\Temporality;
use App\Models\VolatileColumn;
use Illuminate\Http\Request;

class TemporalityController extends Controller
{
    public function create(Request $request){
        $dataRequest = $request->all();

        if($request['id']){
            $temporality_id = $dataRequest['id'];
            $temporality = Temporality::find($dataRequest['id'])->update($dataRequest);
        }else{
            $temporality = Temporality::create($dataRequest);
            $temporality_id = $temporality->id;
        }

        $volatileNames = $request->input('volatile_names', []);
        $volatileValues = $request->input('volatile_values', []);
        VolatileColumn::where('temporality_id', $temporality_id)->delete();

        foreach ($volatileNames as $index => $name) {
            if (!empty($name) && !empty($volatileValues[$index])) {
                VolatileColumn::create([
                    'temporality_id' => $temporality_id,
                    'name' => $name,
                    'value' => $volatileValues[$index],
                ]);
            }
        }

        return redirect('temporalitys');
    }

    public function getVolatileColumns($id)
    {
        $columns = VolatileColumn::where('temporality_id', $id)->get();
        return response()->json($columns);
    }

    public function delete($id){
        Temporality::find($id)->delete();
        return redirect('temporalitys');
    }


}
