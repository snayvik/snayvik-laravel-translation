@extends(config('SnayvikTranslation.extend_blade'))

@section(config('SnayvikTranslation.content_section'))
<div class="container-fluid">
    @include('SnayvikTranslationView::translations.flashes')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body row">                    
                    <div class="form-group col-3">
                        <select class="form-select" name="group" id="choose_group">
                            <option value="">Select group</option>
                            @foreach ($groups as $group)
                                <option {{ !empty($selected_group) && $selected_group == $group ? 'selected' : '' }} value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-9">
                        <p>Choose a group to display the group translations. If no groups are visisble, make sure you have run the migrations and imported the translations. </p>
                    </div>
                </div>
            </div>
            @if(!empty($selected_group))
            <div class="card mb-3">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Key</th>
                                @foreach ($locales as $locale)
                                <th>{{ $locale }}</th>
                                @endforeach
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @include('SnayvikTranslationView::translations.group-table')                            
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Import in database</h5>
                    <hr>
                    <form method="post" action="{{ route('translations.importInDB') }}" class="row">
                        @csrf
                        <div class="form-group col-12">                            
                            <p>The translations which are available in lang directory will be import in the database.</p>
                        </div>
                        <div class="form-group col-8">
                            <select class="form-select" name="replace">
                                <option value="0">Append new translation</option>
                                <option value="1">Replace existing translation</option>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            
                            <button type="submit" class="btn btn-success">Import</button>
                        </div>
                        
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body row"> 
                    <h5>Supported locales</h5>
                    <hr>
                    
                    <div class="form-group col-12">
                        <label>Current supported locales:</label>
                        <ul>
                            @foreach ($locales as $locale)
                                <li>
                                    <a href="{{ route('translations.locale.delete',['locale' => $locale]) }}" onclick="return confirm('Are you sure want to delete?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash text-danger" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </a>&nbsp;
                                    <span>
                                    {{ $locale }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <form method="post" class="form-horizontal row" action="{{ route('translations.locale.store') }}">
                        <label>Enter new locale key:</label>
                        @csrf
                        <div class="form-group col-8">                                                
                            <input name="locale" class="form-control" type="text"/>
                        </div>
                        <div class="form-group col-4">                                                
                            <button type="submit" class="btn btn-success">Add new locale</button>                 
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body"> 
                    <h5>Write in files</h5>
                    <hr>
                    <form method="post" class="form-horizontal" action="{{ route('translations.importInFiles') }}">
                        @csrf
                        <div class="form-group col-3">                                                
                            <button type="submit" class="btn btn-success">Publish all</button>                 
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section(config('SnayvikTranslation.javascript_section'))
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        const choose_group = document.getElementById('choose_group');
        choose_group.addEventListener('change', function(e){
            const value = e.target.value;
            if(value){
                var url = '{{ route("translations.show_group", ":group") }}';
                url = url.replace(':group', value);

                window.location.href = url;
            }
        })


        function groupInit(){
            const noEditables = document.querySelectorAll('#table-body .no-editable');
            var editArea = document.getElementById('editTranslationArea')
            var editLocale = document.getElementById('editTranslationLocale')
            var editKey = document.getElementById('editTranslationKey')            

            for (const [key, value] of Object.entries(noEditables)) {            
                value.addEventListener('click', function (e) {
                    e.preventDefault();
                    showOnlyNotEditable();

                    e.target.nextElementSibling.style.display = 'block';
                    e.target.style.display = 'none';                    
                })
            }

            const cancelTranslationBtns = document.querySelectorAll('#table-body .cancel-translation');
            for (const [key, value] of Object.entries(cancelTranslationBtns)) {            
                value.addEventListener('click', function (e) {
                    e.preventDefault();
                    showOnlyNotEditable()
                });
            }
        }

        function showOnlyNotEditable(){
            const noEditables = document.querySelectorAll('#table-body .no-editable');
            const editables = document.querySelectorAll('#table-body .editable');

            for (const [key, value] of Object.entries(noEditables)) {            
                value.style.display = 'block';                    
            }

            for (const [key, value] of Object.entries(editables)) {            
                value.style.display = 'none';
            }
        }

        groupInit();   
        translationUpdate();  
        
        function translationUpdate(){
            var updateTranslationForm = document.getElementsByClassName('updateTranslationForm');
            for (const [key, updateForm] of Object.entries(updateTranslationForm)) {  
                updateForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const url = e.target.action;
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", url, true);
                    var formData = new FormData(e.target)

                    const data = Object.fromEntries(formData.entries());
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.onreadystatechange = function () {
                        if (this.readyState != 4) return;

                        if (this.status == 200) {
                            document.getElementById('table-body').innerHTML = JSON.parse(xhr.response).html
                            
                            groupInit();
                            translationUpdate()
                        }

                        // end of state change: it can be after some time (async)
                    };

                    xhr.send(JSON.stringify(data));
                })
            }
        }
        
    });

</script>
@endsection