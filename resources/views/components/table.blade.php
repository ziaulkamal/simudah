@props([
    'data',      // koleksi data (paginasi / collection)
    'columns' => [], // array kolom: ['field' => 'Label']
])

<div class="card mt-3">
    <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr>
                    {{-- Header otomatis --}}
                    @foreach ($columns as $field => $label)
                        <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">
                            {{ $label }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $item)
                    <tr class="border-b border-slate-200 dark:border-navy-500">
                        {{-- Kirim variabel $item dan $loop ke slot --}}
                        {{ $slot->with(['item' => $item, 'loop' => $loop]) }}
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="py-4 text-center text-slate-500">
                            Tidak ada data ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="text-xs-plus flex justify-center sm:justify-end text-center sm:text-right text-slate-600 dark:text-navy-100 sm:pr-[calc(var(--margin-x)*-1)]">
            {{ $data->links('components.paginations') }}
        </div>
    @endif
</div>
