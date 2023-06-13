<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePropertyRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function create(CreatePropertyRequest $_request): JsonResponse
    {
        dd($_request);
        $request = $_request->validated();

        dd($request);
        // todo move to service

        $user = $request->user();

        $propertyId = Str::uuid();
        $property = new Property([
            'id' => $propertyId,
            'description' => $request['property']['description'],
            'price_usd' => $request['property']['price'],
            'address' => $request['address']['address'],
            'city' => $request['address']['city'],
            'state' => $request['address']['state'],
            'zip' => $request['address']['zip'],
            'country' => $request['address']['country'],
        ]);

        // process files
        if ($request->hasFile('files')) {
            $path = "aqaro/{$propertyId}";
            Storage::disk('do_spaces')->makeDirectory($path);
            $storage_url = Storage::disk('do_spaces')->url($path);
            foreach ($request->file('files') as $file) {
                Storage::disk('do_spaces')->putFileAs($path, $file, $file->getClientOriginalName());
                $property->files()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $storage_url
                ]);
            }
        }

        $user->properties()->create($property);

        $user->first_name = $request['user']['first_name'];
        $user->last_name = $request['user']['last_name'];
        $user->email = $request['user']['email'];
        $user->save();

        // notify user
//        $user->notify(new PropertyCreated($property));

        return response()->json([
            'message' => 'Property created successfully',
            'property' => $property
        ], 201);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'bedrooms' => 'required|numeric',
            'bathrooms' => 'required|numeric',
            'storeys' => 'required|numeric',
            'garages' => 'required|numeric',
        ]);

        // todo move to service
        $property = Property::findOrFail($request->id);
        $property->update($request->all());

        return response()->json([
            'message' => 'Property updated successfully',
            'property' => $property
        ], 200);
    }

    public function getAll(int $page, int $limit): JsonResponse
    {
        $properties = Property::paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Properties retrieved successfully',
            'properties' => $properties
        ], 200);
    }

    public function getById(string $id): JsonResponse
    {
        $property = Property::findOrFail($id);

        return response()->json($property,200);
    }
}
