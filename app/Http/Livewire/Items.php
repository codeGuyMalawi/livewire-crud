<?php

namespace App\Http\Livewire;

use session;
use App\models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    public $active;
    public $searchItem;
    public $sortBy = 'id';
    public $sortAsc = true;
    public $item;

    public $confirmingItemDeletion = false;
    public $confirmingItemAdd = false;
   

    //these are used to change the url when the search field or active field it changed
    //where as the ['except'=>false] is used only change the url field when the active field is not false
    protected $queryString = [

        'active' => ['except' => false],
        'searchItem' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortAsc' => ['except' => true],

    ];

    protected $rules = [
        'item.name' => 'required|string|min:4',
        'item.price' => 'required|numeric|between:1,1000',
        'item.status' => 'boolean',
    ];

    public function render()
    {
        $items = Item::where('user_id', auth()->user()->id)

        //search function
            ->when($this->searchItem, function ($query) {
                return $query->where(function ($query) {
                    return $query->where('name', 'like', '%' . $this->searchItem . '%')
                        ->orWhere('price', 'like', '%' . $this->searchItem . '%');

                });

            })
            ->when($this->active, function ($query) {
                return $query->where('status', 1);
            })
            ->orderBY($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC');

        //for debugging processes
        //  $query = $items->toSql();

        $items = $items->paginate(10);

        return view('livewire.items', [

            'items' => $items,
            //   'query' => $query
        ]);
    }

    //this functon resets a page  to show only the active elements
    public function updatingActive()
    {

        $this->resetPage();
    }

    //this functon resets a page  to show only the searched elements
    public function updatingSearchItem()
    {

        $this->resetPage();
    }

    public function sortBy($field)
    {

        if ($field == $this->sortBy) {

            $this->sortAsc = !$this->sortAsc;
        }

        $this->sortBy = $field;
    }

    //delete modal and action
    public function confirmItemDeletion($id)
    {

        $this->confirmingItemDeletion = $id;
    }

    public function deleteItem(Item $item)
    {

        $item->delete();
        $this->confirmingItemDeletion = false;
        session()->flash('message','Item Deleted Successfully!');

    }

    //Add Modal And Action

    public function confirmItemAdd()
    {

        $this->reset(['item']);
        $this->confirmingItemAdd = true;
    }

    public function saveItem()
    {

        $this->validate();

        if (isset($this->item->id)) {
            
            $this->item->save();
            session()->flash('message','Item Updated Successfully!');

        } else {

            auth()->user()->items()->create([

                'name' => $this->item['name'],
                'price' => $this->item['price'],
                'status' => $this->item['status'] ?? 0,

            ]);

            session()->flash('message','Item Added Successfully!');
        }

        $this->confirmingItemAdd = false;
    }

//edit modal and action

    public function confirmItemEdit(Item $item)
    {

        $this->item = $item;
        $this->confirmingItemAdd = true;
    }

}
