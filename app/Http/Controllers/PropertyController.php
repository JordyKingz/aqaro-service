<?php

namespace App\Http\Controllers;

use App\API\CoingeckoApi;
use App\Http\Requests\CreatePropertyRequest;
use App\Models\Enums\FileType;
use App\Models\Property;
use App\Models\PropertyFiles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Aws\S3\S3Client;


class PropertyController extends Controller
{
    protected $coingeckoApi;

    public function __construct(CoingeckoApi $coingeckoApi)
    {
        $this->coingeckoApi = $coingeckoApi;
    }

    public function create(CreatePropertyRequest $_request): JsonResponse
    {
        $request = $_request->validated();

        $user = $_request->user();

//        $price = $this->coingeckoApi->getTokenPrice('ethereum', 'usd');
//        $priceInDollars = $price['ethereum']['usd'] * $request['property']['price'];

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

        $user->properties()->save($property);

        // process files
        if (Arr::has($request, 'files')) {
            $path = "aqaro/{$propertyId}";
            Storage::disk('do_spaces')->makeDirectory($path);
            $storage_url = Storage::disk('do_spaces')->url($path);
            $files = $request['files'];

            foreach ($files as $file) {
                Storage::disk('do_spaces')->putFileAs($path, $file, $file->getClientOriginalName());

                $propertyFile = new PropertyFiles([
                    'name' => $file->getClientOriginalName(),
                    'storage_url' => $storage_url . '/' . $file->getClientOriginalName(),
                    'type' => FileType::fromMimeType($file->getMimeType()),
                ]);

                $property->files()->save($propertyFile);
            }
        }

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
            'sc_id' => 'required|numeric'
        ]);

        // todo move to service
        $property = Property::findOrFail($request->id);
        $property->sc_id = $request->sc_id;
        $property->save();

        return response()->json([
            'message' => 'Property updated successfully',
            'property' => $property
        ], 200);
    }

    public function getAll(int $page, int $limit): JsonResponse
    {
        $properties = Property::paginate($limit, ['*'], 'page', $page);

//        foreach($properties->files as $file) {
//            $file->storage_url = Storage::disk('do_spaces')->temporaryUrl($file->storage_url, now()->addMinutes(5));
//        }

        return response()->json([
            'message' => 'Properties retrieved successfully',
            'properties' => $properties
        ], 200);
    }

    public function getById(string $id): JsonResponse
    {
        $property = Property::findOrFail($id);

        $path = "aqaro/{$id}";
        $files = Storage::disk('do_spaces')->files($path);

        $temporaryUrls = [];
        foreach ($files as $file) {
            $url = Storage::disk('do_spaces')->temporaryUrl($file, now()->addMinutes(5));
            $temporaryUrls[] = $url;
        }

        $dto = [
            "property" => $property,
            "files" => $temporaryUrls
        ];

        return response()->json($dto,200);
    }

    public function getThumbnail($sc_id) {
        $property = Property::where('sc_id', $sc_id)->firstOrFail();

        $propertyFile = PropertyFiles::where('property_id', $property->id)->firstOrFail();

        $path = "aqaro/{$property->id}";
        $files = Storage::disk('do_spaces')->files($path);

        $url = '';
        for($i = 0; $i < 1; $i++) {
            $url = Storage::disk('do_spaces')->temporaryUrl($files[$i], now()->addMinutes(5));
        }

        $dto = [
            "thumbnail" => $url,
        ];
        return response()->json($dto,200);
    }
}
