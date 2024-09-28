<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Escolas/Save')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("POST")
                    @if(session('success'))
                    <div class="col-sm-12 shadow p-2 bg-success text-white">
                        <strong>{{session('success')}}</strong>
                    </div>
                    @elseif(session('error'))
                    <div class="col-sm-12 shadow p-2 bg-danger text-white">
                        <strong>{{session('error')}}</strong>
                    </div>
                    <br>
                    @endif
                    @if(isset($Registro->id))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    <input type="hidden" name="oldFoto" value="{{$Registro->Foto}}">
                    @endif
                    <input type="hidden" name="IDOrg" value="{{Auth::user()->id_org}}">
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{ isset($Registro->Foto) ? url('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Registro->id . '/' . $Registro->Foto) : asset('img/escolaAvatar.jpg') }}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                <label class="form-label text-white m-1" for="customFile2">Upload Foto</label>
                                <input type="file" name="Foto" class="form-control d-none" id="customFile2" onchange="displaySelectedImage(event, 'selectedAvatar')" accept="image/jpg,image/png,image/jpeg" {{!isset($Registro) ? 'required' : ''}} />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome da escola</label>
                            <input type="text" name="Nome" class="form-control @error('Organizacao') is-invalid @enderror" maxlength="100" required value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Quantidade de Vagas</label>
                            <input type="text" name="QTVagas" class="form-control" value="{{isset($Registro->QTVagas) ? $Registro->QTVagas : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Telefone</label>
                            <input type="text" name="Telefone" class="form-control" value="{{isset($Registro->Telefone) ? $Registro->Telefone : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>E-mail</label>
                            <input type="email" name="Email" class="form-control" maxlength="50" required value="{{isset($Registro->Email) ? $Registro->Email : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" maxlength="9" minlength="9" required value="{{isset($Registro->CEP) ? $Registro->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" required value="{{isset($Registro->Rua) ? $Registro->Rua : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="60" required value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="60" value="{{isset($Registro->Bairro) ? $Registro->Bairro : ''}}" required>
                        </div>
                        <div class="col-sm-2">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->UF) ? $Registro->UF : ''}}" minlength="2" required>
                        </div>
                        <div class="col-sm-2">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Registro->Numero) ? $Registro->Numero : ''}}" required>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Escolas/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        function displaySelectedImage(event, elementId) {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
        $('input[name=CEP]').on("change",function(e){
            if( $(this).val().length == 9){
                var cep = $(this).val();
                var url = "https://viacep.com.br/ws/"+cep+"/json/";
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    success: function(dados){
                        $("input[name=UF]").val(dados.uf).change();
                        $("input[name=Cidade]").val(dados.localidade);
                        $("input[name=Bairro]").val(dados.bairro);
                        $("input[name=Rua]").val(dados.logradouro);
                    }
                })
            }            
        })
        //
        $("input[name=CEP]").inputmask('99999-999')
        $("input[name=Telefone]").inputmask('(99) 9999-9999')
    </script>
</x-educacional-layout>