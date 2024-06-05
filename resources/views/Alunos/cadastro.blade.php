<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Save')}}" method="POST" enctype="multipart/form-data">
                @csrf
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
                @if(isset($Registro->IDFerias))
                <input type="hidden" value="{{$Registro->IDFerias}}" name="id">
                @endif
                <div class="col-sm-12 p-2">
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{asset('img/kidAvatar.png')}}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                <label class="form-label text-white m-1" for="customFile2">Upload Foto 3x4</label>
                                <input type="file" name="Foto" class="form-control d-none" id="customFile2" onchange="displaySelectedImage(event, 'selectedAvatar')" accept="image/jpg,image/png,image/jpeg" required />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome completo do Aluno</label>
                            <input type="text" class="form-control" value="" name="Nome" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG do Aluno</label>
                            <input type="text" class="form-control" value="" name="RG" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF do Aluno</label>
                            <input type="text" class="form-control" value="" name="CPF" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email do Aluno</label>
                            <input type="text" class="form-control" value="" name="Email" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nascimento do Aluno</label>
                            <input type="date" class="form-control" value="" name="Nascimento" required> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                        <label>Nome completo do Responsavel</label>
                        <input type="text" class="form-control" value="" name="NMResponsavel" required> 
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Responsavel</label>
                            <input type="text" class="form-control" value="" name="RGPais" required> 
                        </div>
                        <div class="col-sm-3">
                            <label>CPF do Responsavel</label>
                            <input type="text" class="form-control" value="" name="CPFResponsavel" required> 
                        </div>
                        <div class="col-sm-3">
                            <label>Email do Responsavel</label>
                            <input type="email" class="form-control" value="" name="EmailResponsavel" required> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" required value="{{isset($Registro->Cidade) ? $Registro->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Registro->Bairro) ? $Registro->Rua : ''}}" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Registro->UF) ? $Registro->Bairro : ''}}" minlength="2" required>
                        </div>
                        <div class="col-sm-1">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->Numero) ? $Registro->UF : ''}}" required>
                        </div>
                        <div class="col-sm-1">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Registro->Numero) ? $Registro->Numero : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Responsavel</label>
                            <input type="text" name="CLResponsavel" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Celular" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>RG dos Pais</label>
                            <input type="file" class="form-control" name="RGPaisAnexo" accept="application/pdf" required>
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Aluno</label>
                            <input type="file" class="form-control" name="AnexoRG" accept="application/pdf" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Comprovante de Residência</label>
                            <input type="file" class="form-control" name="CResidencia" accept="application/pdf" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Histórico Escolar</label>
                            <input type="file" class="form-control" name="Historico" accept="application/pdf">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Turma</label>
                            <select class="form-control" name="IDTurma" required>
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->id}}">{{$t->Nome." (".$t->Serie.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Possui NEE</label>
                            <select class="form-control" name="NEE" >
                                <option value="1">Sim</option>
                                <option value="0">Mão</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control" name="AMedico">
                                <option value="1">Sim</option>
                                <option value="0">Mão</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control" name="APsicologico">
                                <option value="1">Sim</option>
                                <option value="0">Mão</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Vencimento da Matrícula</label>
                            <input type="date" name="Vencimento" class="form-control" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Tem Alergia?</label>
                            <select class="form-control" name="Alergia">
                                <option value="1">Sim</option>
                                <option value="0">Mão</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control" name="Transporte">
                                <option value="1">Sim</option>
                                <option value="0">Mão</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control" name="BolsaFamilia">
                                <option value="1">Sim</option>
                                <option value="0">Mão</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn btn-fr">Salvar</button>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-light" href="{{route('Alunos/index')}}">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
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
        $("input[name=Celular]").inputmask('(99) 9 9999-9999')
        $("input[name=CLResponsavel]").inputmask('(99) 9 9999-9999')
        $("input[name=CPF]").inputmask('999.999.999-99')
        $("input[name=CPFAluno]").inputmask('999.999.999-99')
        $("input[name=CPFResponsavel]").inputmask('999.999.999-99')
        $("input[name=RGResponsavel]").inputmask('99-999-999')
        $("input[name=RGPais]").inputmask('99.999.999')
        $("input[name=RG]").inputmask('99.999.999')
        //
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
        //
    </script>
</x-educacional-layout>