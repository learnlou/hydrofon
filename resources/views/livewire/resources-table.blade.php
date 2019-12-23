<div>
    <table class="table">
        <thead>
            <th class="table-column-check">
                <input
                    type="checkbox"
                    {{ count($this->selectedRows) === count($items) ? 'checked="checked"' : '' }}
                    wire:click="$emit('selectAll', $event.target.checked)"
                />
            </th>
            <th><a href="{{ route('resources.index', ['sort' => (request('sort') === 'name' || request()->has('sort') === false ? '-' : '') . 'name'] + request()->except('page')) }}">Name</a></th>
            <th><a href="{{ route('resources.index', ['sort' => (request('sort') === 'description' ? '-' : '') . 'description'] + request()->except('page')) }}">Description</a></th>
            <th><a href="{{ route('resources.index', ['sort' => (request('sort') === 'is_facility' ? '-' : '') . 'is_facility'] + request()->except('page')) }}">Facility</a></th>
            <th>&nbsp;</th>
        </thead>

        <tbody>
            @forelse($items as $item)
                @if($this->isEditing === $item->id)
                    <tr class="is-editing">
                        <td data-title="&nbsp;">&nbsp;</td>
                        <td data-title="Name">
                            <input
                                value="{{ $item->name }}"
                                type="text"
                                class="field"
                                wire:model.debounce.500ms="editValues.name"
                            />

                            @error('editValues.name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </td>
                        <td data-title="Description">
                            <textarea
                                cols="20"
                                rows="1"
                                class="field"
                                wire:model.debounce.500ms="editValues.description"
                            >{{ $item->description }}</textarea>

                            @error('editValues.description')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </td>
                        <td data-title="Facility">
                            <input
                                {{ $item->is_facility ? 'checked="checked"' : '' }}
                                type="checkbox"
                                wire:model.debounce.500ms="editValues.is_facility"
                            />

                            @error('editValues.is_facility')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </td>
                        <td data-title="&nbsp;" class="table-actions">
                            <a
                                class="btn btn-primary"
                                wire:click.prevent="$emit('save')"
                                wire:loading.attr="disabled"
                            >Save</a>

                            <a
                                class="btn"
                                wire:click.prevent="$set('isEditing', false)"
                            >Cancel</a>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td data-title="&nbsp;">
                            <input
                                type="checkbox"
                                value="{{ $item->id }}"
                                {{ in_array($item->id, $this->selectedRows) ? 'checked="checked"' : '' }}
                                wire:click="$emit('select', {{ $item->id }}, $event.target.checked)"
                            />
                        </td>
                        <td data-title="Name">
                            <a href="{{ route('resources.edit', $item) }}">{{ $item->name }}</a>
                        </td>
                        <td data-title="Description">
                            {{ $item->description }}
                        </td>
                        <td data-title="Facility">
                            <input
                                {{ $item->is_facility ? 'checked="checked"' : '' }}
                                disabled="disabled"
                                type="checkbox"
                            />
                        </td>
                        <td data-title="&nbsp;" class="table-actions">
                            <div>
                                <a
                                    href="{{ route('resources.edit', $item) }}"
                                    title="Edit"
                                    wire:click.prevent="$emit('edit', {{ $item->id }})"
                                >Edit</a>
                            </div>

                            <div>
                                {!! Form::open(['route' => ['resources.identifiers.index', $item->id], 'method' => 'GET' ]) !!}
                                    <button type="submit" title="Identifiers">
                                        Identifiers
                                    </button>
                                {!! Form::close() !!}
                            </div>

                            <div>
                                {!! Form::model($item, ['route' => ['resources.destroy', $item->id], 'method' => 'DELETE' ]) !!}
                                <button
                                    type="submit"
                                    title="Delete"
                                    wire:click.prevent="$emit('delete', {{ $item->id }})"
                                    wire:loading.attr="disabled"
                                >Delete</button>
                                {!! Form::close() !!}
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="5">No resources was found.</td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5">
                    <div class="flex justify-end">
                        <form>
                            <button
                                {{ count($this->selectedRows) === 0 ? 'disabled="disabled"' : '' }}
                                class="btn"
                                wire:click.prevent="$emit('delete', false, true)"
                            >Delete</button>
                        </form>
                    </div>
                </th>
            </tr>
        </tfoot>
    </table>

    {{ $items->appends(['filter' => request()->get('filter'), 'sort' => request()->get('sort')])->links() }}
</div>

@include('livewire.partials.table-scripts')
