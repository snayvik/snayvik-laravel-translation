@foreach ($keys as $index => $key)
    <tr>
        <td>{{ $key }}</td>

        @foreach ($locales as $locale)        
            <td>                
                <a href="#" class="no-editable {{ !empty($translations[$locale][$key]) ? '' : 'text-danger' }}" data-key="{{ $key }}" data-locale="{{ $locale }}" data-content="{!! $translations[$locale][$key] ?? ''  !!}">
                    {!! !empty($translations[$locale][$key]) ?  $translations[$locale][$key] : 'Empty' !!} 
                </a>
                <div class="editable" style="display: none">
                    <form method="post" action="{{ route('translations.store') }}" class="d-flex updateTranslationForm">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $locale }}" />
                        <input type="hidden" name="group" value="{{ $selected_group }}" />
                        <input type="hidden" name="key" value="{{ $key }}" />
                        <input type="text" class="form-control ms-1" name="value" value="{{ !empty($translations[$locale][$key])  ? $translations[$locale][$key] : '' }}" />
                        <button type="submit" class="btn btn-success btn-sm ms-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                            </svg>
                        </button>
                        
                        <button type="button" class="ms-1 btn btn-danger btn-sm cancel-translation">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </td>
        @endforeach

        <td>
            <a class="delete-translation" href="{{ route('translations.delete',['group' => $selected_group, 'key' => $key]) }}" onclick="return confirm('Are you sure want to delete?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash text-danger" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                </svg>
            </a>
        </td>
    </tr>
@endforeach