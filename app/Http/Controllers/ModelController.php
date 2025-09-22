<?php

namespace App\Http\Controllers;

use App\Models\StrcModel;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['model_path' => 'required|string']);

        $modelPath = $request->input('model_path');

        $model = StrcModel::where('file_path', $modelPath)->first();

        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

            
        return response()->json([
            'id' => $model->id,
            'name' => $model->name,
            'file_path' => $model->file_path,
            'project_id' => $model->project_id,
        ]);
    }
}
