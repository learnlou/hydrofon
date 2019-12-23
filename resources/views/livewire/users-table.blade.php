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
            <th><a href="{{ route('users.index', ['sort' => (request('sort') === 'email' || request()->has('sort') === false ? '-' : '') . 'email'] + request()->except('page')) }}">E-mail</a></th>
            <th><a href="{{ route('users.index', ['sort' => (request('sort') === 'name' ? '-' : '') . 'name'] + request()->except('page')) }}">Name</a></th>
            <th>&nbsp;</th>
        </thead>

        <tbody>
            @forelse($items as $item)
                @if($this->isEditing === $item->id)
                    <tr class="is-editing">
                        <td data-title="&nbsp;">&nbsp;</td>
                        <td data-title="E-mail">
                            <input
                                value="{{ $item->email }}"
                                type="email"
                                class="field"
                                wire:model.debounce.500ms="editValues.email"
                            />

                            @error('editValues.email')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </td>
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
                        <td data-title="E-mail">
                            <a href="{{ route('users.edit', $item) }}">{{ $item->email }}</a>
                        </td>
                        <td data-title="Name">
                            <a href="{{ route('users.edit', $item) }}">{{ $item->name }}</a>
                        </td>
                        <td data-title="&nbsp;" class="table-actions">
                            <div>
                                <a
                                    href="{{ route('users.edit', $item) }}"
                                    title="Edit"
                                    wire:click.prevent="$emit('edit', {{ $item->id }})"
                                >Edit</a>
                            </div>

                            <div>
                                {!! Form::open(['route' => ['users.identifiers.index', $item->id], 'method' => 'GET' ]) !!}
                                    <button type="submit" title="Identifiers">
                                        Identifiers
                                    </button>
                                {!! Form::close() !!}
                            </div>

                            <div>
                                {!! Form::model($item, ['route' => ['users.destroy', $item->id], 'method' => 'DELETE' ]) !!}
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
                    <td colspan="4">No users was found.</td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4">
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