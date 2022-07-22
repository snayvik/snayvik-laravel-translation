@extends('layouts.app')

@section('content')
@include('SnayvikTranslationView::translations.flashes')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Key</th>
                                @foreach ($locals as $locale)
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
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    window.addEventListener('DOMContentLoaded', (event) => {

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

                    if(e.target.dataset.locale && e.target.dataset.key){
                        if(e.target.dataset.content){
                            editArea.value = e.target.dataset.content;
                        }else{
                            editArea.value = '';
                        }

                        console.log(e.target);
                        editLocale.value = e.target.dataset.locale;
                        editKey.value = e.target.dataset.key;                            
                    }
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
    })    
</script>
@endsection