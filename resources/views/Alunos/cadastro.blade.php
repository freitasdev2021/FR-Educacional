<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
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
                @if(isset($Registro->IDAluno) && isset($Registro->IDMatricula))
                <input type="hidden" value="{{$Registro->IDAluno}}" name="IDAluno">
                <input type="hidden" value="{{$Registro->IDMatricula}}" name="IDMatricula">
                <input type="hidden" name="oldAnexoRG" value="{{$Registro->AnexoRG}}">
                <input type="hidden" name="oldRGPaisAnexo" value="{{$Registro->RGPaisAnexo}}">
                <input type="hidden" name="oldCResidencia" value="{{$Registro->CResidencia}}">
                <input type="hidden" name="oldHistorico" value="{{$Registro->Historico}}">
                <input type="hidden" name="oldFoto" value="{{$Registro->Foto}}">
                <input type="hidden" name="CDPasta" value="{{$Registro->CDPasta}}">
                @endif
                <div class="col-sm-12 p-2">
                @if(in_array(Auth::user()->tipo,[4,2]))
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{ isset($Registro->Foto) ? url('storage/organizacao_' . Auth::user()->id_org . '_alunos/aluno_' . $Registro->CDPasta . '/' . $Registro->Foto) : asset('img/kidAvatar.png') }}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                <label class="form-label text-white m-1" for="customFile2">Upload Foto 3x4</label>
                                <input type="file" name="Foto" class="form-control d-none" id="customFile2" onchange="displaySelectedImage(event, 'selectedAvatar')" accept="image/jpg,image/png,image/jpeg" {{!isset($Registro) ? 'required' : ''}} />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome completo do Aluno</label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}" {{isset($Registro->Nome) ? 'disabled' : 'required'}}> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RG) ? $Registro->RG : ''}}" {{isset($Registro->RG) ? 'disabled' : 'required'}} name="RG"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPF) ? $Registro->CPF : ''}}" name="CPF" {{isset($Registro->CPF) ? 'disabled' : 'required'}}> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Email) ? $Registro->Email : ''}}" name="Email" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nascimento do Aluno</label>
                            <input type="date" class="form-control" value="{{isset($Registro->Nascimento) ? $Registro->Nascimento : ''}}" name="Nascimento" {{isset($Registro->Nascimento) ? 'disabled' : 'required'}}> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                        <label>Nome completo do Responsavel</label>
                        <input type="text" class="form-control" value="{{isset($Registro->NMResponsavel) ? $Registro->NMResponsavel : ''}}" name="NMResponsavel" required> 
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RGPais) ? $Registro->RGPais : ''}}" name="RGPais" required> 
                        </div>
                        <div class="col-sm-3">
                            <label>CPF do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPFResponsavel) ? $Registro->CPFResponsavel : ''}}" name="CPFResponsavel" required> 
                        </div>
                        <div class="col-sm-3">
                            <label>Email do Responsavel</label>
                            <input type="email" class="form-control" value="{{isset($Registro->EmailResponsavel) ? $Registro->EmailResponsavel : ''}}" name="EmailResponsavel" required> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" required value="{{isset($Registro->CEP) ? $Registro->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Registro->Rua) ? $Registro->Rua : ''}}" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Registro->Bairro) ? $Registro->Bairro : ''}}" minlength="2" required>
                        </div>
                        <div class="col-sm-1">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->UF) ? $Registro->UF : ''}}" required>
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
                            <input type="text" name="CLResponsavel" class="form-control" maxlength="50" value="{{isset($Registro->CLResponsavel) ? $Registro->CLResponsavel : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Celular" class="form-control" maxlength="50" value="{{isset($Registro->Celular) ? $Registro->Celular : ''}}" minlength="3" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>RG dos Pais</label>
                            <input type="file" class="form-control" name="RGPaisAnexo" accept="application/pdf" {{!isset($Registro) ? 'required' : ''}}>
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Aluno</label>
                            <input type="file" class="form-control" name="AnexoRG" accept="application/pdf" {{!isset($Registro) ? 'required' : ''}}>
                        </div>
                        <div class="col-sm-3">
                            <label>Comprovante de Residência</label>
                            <input type="file" class="form-control" name="CResidencia" accept="application/pdf" {{!isset($Registro) ? 'required' : ''}}>
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
                                <option value="{{$t->id}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->id ? 'selected' : ''}}>{{$t->Nome." (".$t->Serie.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Possui NEE</label>
                            <select class="form-control" name="NEE" >
                                <option value="1" {{isset($Registro->NEE) && $Registro->NEE == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->NEE) && $Registro->NEE == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control" name="AMedico">
                                <option value="1" {{isset($Registro->AMedico) && $Registro->AMedico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->AMedico) && $Registro->AMedico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control" name="APsicologico">
                                <option value="1" {{isset($Registro->APsicologico) && $Registro->APsicologico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->APsicologico) && $Registro->APsicologico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Vencimento da Matrícula</label>
                            <input type="date" name="Vencimento" class="form-control" value="{{isset($Registro->Vencimento) ? $Registro->Vencimento : ''}}" required>
                        </div>
                        <div class="col-sm-2">
                            <label>Tem Alergia?</label>
                            <select class="form-control" name="Alergia">
                                <option value="1" {{isset($Registro->Alergia) && $Registro->Alergia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Alergia) && $Registro->Alergia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control" name="Transporte">
                                <option value="1" {{isset($Registro->Transporte) && $Registro->Transporte == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Transporte) && $Registro->Transporte == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control" name="BolsaFamilia">
                                <option value="1" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Quilombola</label>
                            <select class="form-control" name="Quilombola">
                                <option value="1" {{isset($Registro->Quilombola) && $Registro->Quilombola == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Quilombola) && $Registro->Quilombola == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>ensino religioso, Direito de uso de Imagens e educação física</label>
                            <select class="form-control" name="Autorizacao">
                                <option value="1" {{isset($Registro->Autorizacao) && $Registro->Autorizacao == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Autorizacao) && $Registro->Autorizacao == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        @if(in_array(Auth::user()->tipo,[4]))
                        <div class="col-auto">
                            <button class="btn btn-fr">Salvar</button>
                        </div>
                        @if(isset($Registro) && $Vencimento->lt($Hoje) && $Registro->ANO <= date('Y'))
                        <div class="col-auto">
                            <button class="btn btn-warning text-white btn-renovar" type="button">Renovar</button>
                        </div>
                        @endif
                        <div class="col-auto">
                            <a class="btn btn-light" href="{{route('Alunos/index')}}">Cancelar</a>
                        </div>
                        @endif
                    </div>
                    @elseif(in_array(Auth::user()->tipo,[5,6]))
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{!isset($Registro->Foto) ? asset('img/kidAvatar.png') : url("storage/organizacao_".Auth::user()->id_org."alunos/aluno_$Registro->CDPasta/$Registro->Foto")}}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome completo do Aluno</label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RG) ? $Registro->RG : ''}}" name="RG" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPF) ? $Registro->CPF : ''}}" name="CPF" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Email) ? $Registro->Email : ''}}" name="Email" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nascimento do Aluno</label>
                            <input type="date" class="form-control" value="{{isset($Registro->Nascimento) ? $Registro->Nascimento : ''}}" name="Nascimento" disabled> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                        <label>Nome completo do Responsavel</label>
                        <input type="text" class="form-control" value="{{isset($Registro->NMResponsavel) ? $Registro->NMResponsavel : ''}}" name="NMResponsavel" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RGPais) ? $Registro->RGPais : ''}}" name="RGPais" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>CPF do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPFResponsavel) ? $Registro->CPFResponsavel : ''}}" name="CPFResponsavel" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>Email do Responsavel</label>
                            <input type="email" class="form-control" value="{{isset($Registro->EmailResponsavel) ? $Registro->EmailResponsavel : ''}}" name="EmailResponsavel" disabled> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" disabled value="{{isset($Registro->CEP) ? $Registro->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Registro->Rua) ? $Registro->Rua : ''}}" disabled>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Registro->Bairro) ? $Registro->Bairro : ''}}" minlength="2" disabled>
                        </div>
                        <div class="col-sm-1">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->UF) ? $Registro->UF : ''}}" disabled>
                        </div>
                        <div class="col-sm-1">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Registro->Numero) ? $Registro->Numero : ''}}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Responsavel</label>
                            <input type="text" name="CLResponsavel" class="form-control" maxlength="50" value="{{isset($Registro->CLResponsavel) ? $Registro->CLResponsavel : ''}}" minlength="3" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Celular" class="form-control" maxlength="50" value="{{isset($Registro->Celular) ? $Registro->Celular : ''}}" minlength="3" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Turma</label>
                            <select class="form-control" name="IDTurma" disabled>
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->id}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->id ? 'selected' : ''}}>{{$t->Nome." (".$t->Serie.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Possui NEE</label>
                            <select class="form-control" name="NEE" disabled>
                                <option value="1" {{isset($Registro->NEE) && $Registro->NEE == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->NEE) && $Registro->NEE == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control" name="AMedico" disabled>
                                <option value="1" {{isset($Registro->AMedico) && $Registro->AMedico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->AMedico) && $Registro->AMedico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control" name="APsicologico" disabled>
                                <option value="1" {{isset($Registro->APsicologico) && $Registro->APsicologico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->APsicologico) && $Registro->APsicologico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Vencimento da Matrícula</label>
                            <input type="date" name="Vencimento" class="form-control" value="{{isset($Registro->Vencimento) ? $Registro->Vencimento : ''}}" disabled>
                        </div>
                        <div class="col-sm-3">
                            <label>Tem Alergia?</label>
                            <select class="form-control" name="Alergia" disabled>
                                <option value="1" {{isset($Registro->Alergia) && $Registro->Alergia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Alergia) && $Registro->Alergia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control" name="Transporte" disabled>
                                <option value="1" {{isset($Registro->Transporte) && $Registro->Transporte == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Transporte) && $Registro->Transporte == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control" name="BolsaFamilia" disabled>
                                <option value="1" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
            </form>
            @if(isset($Registro) && $Vencimento->lt($Hoje) && $Registro->ANO <= date('Y'))
            <form class="form-controls" id="formRenova" method="POST" action="{{route('Alunos/Renovar')}}" style="display:hidden">
                @csrf
                <input type="hidden" name="IDAluno" value="{{$Registro->IDAluno}}">  
            </form>
            @endif
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
        $('.btn-renovar').on("click",function(){
            if(confirm("Deseja Renovar a Matrícula do Aluno?")){
                $("#formRenova").submit()
            }
        })
        //
    </script>
</x-educacional-layout>