<?php

namespace App\Livewire;

use App\Models\Branch;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SearchBar extends Component
{

    public $searchTerm = '';
    public $searchResults = [];

    public function mount()
    {
        $this->searchTerm = '';
    }

    #[On('updateSearchTerm')]
    public function updateSearchTerm($value)
    {
        $this->searchTerm = $value;
        $this->updatedSearchTerm();
    }



    public function updatedSearchTerm()
    {
        logger('searchTerm: ' . $this->searchTerm);
        $term = strtolower($this->searchTerm);

        $results = Branch::select(
            'br_id',
            'br_name',
            'br_address',
            'br_subdistrict',
            'br_district',
            'br_province',
            'br_postalcode',
            DB::raw('ST_Y(br_longlat) as lat'),
            DB::raw('ST_X(br_longlat) as lng')
        )
            ->where('br_name', 'like', "%{$term}%")
            ->orWhere('br_address', 'like', "%{$term}%")
            ->orWhere('br_subdistrict', 'like', "%{$term}%")
            ->orWhere('br_district', 'like', "%{$term}%")
            ->orWhere('br_province', 'like', "%{$term}%")
            ->orWhere('br_postalcode', 'like', "%{$term}%")
            ->limit(10)
            ->get();

        $this->searchResults = $results->map(function ($branch) {
            $address = $branch->br_address . ' ' . $branch->br_subdistrict . ' ' . $branch->br_district . ' ' . $branch->br_province . ' ' . $branch->br_postalcode;

            return [
                'name' => "{$branch->br_name}",
                'address' => "{$address}",
                'lat' => $branch->lat,
                'long' => $branch->lng,
            ];
        })->toArray();
    }

    public function selectLocation($lat, $long)
    {

        $this->dispatch('locationsUpdate', [[(float)$long, (float)$lat]]);
        $this->searchResults = [];
        $this->searchTerm = '';
    }
    public function render()
    {
        return view('livewire.search-bar');
    }
}
