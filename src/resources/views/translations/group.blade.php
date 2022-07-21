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

<div class="modal fade" id="editTranslationModal" tabindex="-1" aria-labelledby="editTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        {{-- <div class="modal-header">          
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div> --}}
        <div class="modal-body">
            <form id="updateTranslationForm" action="{{ route('translations.store') }}">
                <div class="form-group mb-3">
                    @csrf
                    <input type="hidden" name="locale" id="editTranslationLocale" />
                    <input type="hidden" name="group" id="editTranslationGroup" value="{{ $group }}" />
                    <input type="hidden" name="key" id="editTranslationKey" />
                    <textarea class="form-control" rows="4" name="value" id="editTranslationArea"></textarea>
                </div>

                <div class="form-group float-end">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="updateTranslationButton">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </button>
                </div>
                
            </form>
        </div>       
      </div>
    </div>
  </div>
@endsection


@section('js')
    <script>
        var editModal = new bootstrap.Modal(document.getElementById('editTranslationModal'))
        

        function groupInit(){
            const anchors = document.querySelectorAll('#table-body .edit-translation');
            var editArea = document.getElementById('editTranslationArea')
            var editLocale = document.getElementById('editTranslationLocale')
            var editKey = document.getElementById('editTranslationKey')            

            for (const [key, value] of Object.entries(anchors)) {            
                value.addEventListener('click', function (e) {
                    e.preventDefault();
                    if(e.target.dataset.locale && e.target.dataset.key){
                        if(e.target.dataset.content){
                            editArea.value = e.target.dataset.content;
                        }else{
                            editArea.value = '';
                        }

                        console.log(e.target);
                        editLocale.value = e.target.dataset.locale;
                        editKey.value = e.target.dataset.key;
                        editModal.show()
                    }
                })
            }
        }

        groupInit();

        var updateTranslationForm = document.getElementById('updateTranslationForm')        
        updateTranslationForm.addEventListener('submit', function (e) {
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
                    editModal.hide()

                    groupInit();
                }

                // end of state change: it can be after some time (async)
            };

            xhr.send(JSON.stringify(data));
        })

        
    </script>
@endsection