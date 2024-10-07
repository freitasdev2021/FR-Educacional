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
                <form action="{{route('Auxiliares/Save')}}" method="POST">
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
                    <input type="hidden" name="IDUser" value="{{$Registro->IDUser}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-9">
                            <label>Local de Trabalho</label>
                            <select name="IDEscola" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="0">Secretaría Municipal de Educação</option>
                                @foreach($Escolas as $e)
                                    <option value="{{$e->id}}" {{(isset($Registro->IDEscola) && $Registro->IDEscola == $e->id) ? 'selected' : ''}}>{{$e->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Cargo</label>
                            <select name="Tipo" class="form-control" required>
                                <option value="">Selecione</option>
                                <optgroup label="Escolas">
                                    <option value="5.5" {{isset($Registro) && $Registro->Tipo == "5.5" ? 'selected' : ''}}>Auxiliar Educacional</option>
                                    <option value="4.5" {{isset($Registro) && $Registro->Tipo == "4.5" ? 'selected' : ''}}>Auxiliar Administrativo</option>
                                </optgroup>
                                <optgroup label="Secretaría">
                                    <option value="2.5" {{isset($Registro) && $Registro->Tipo == "2.5" ? 'selected' : ''}}>Auxiliar Administrativo</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome</label>
                            <input type="text" name="Nome" class="form-control" maxlength="50" required value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Celular</label>
                            <input type="text" name="Celular" class="form-control" value="{{isset($Registro->Celular) ? $Registro->Celular : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Email</label>
                            <input type="email" name="Email" class="form-control" maxlength="50" required value="{{isset($Registro->Email) ? $Registro->Email : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Data de Admissão</label>
                            <input type="date" name="Admissao" class="form-control" required value="{{isset($Registro->Admissao) ? $Registro->Admissao : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Término do Contrato</label>
                            <input type="date" name="TerminoContrato" class="form-control" required value="{{isset($Registro->TerminoContrato) ? $Registro->TerminoContrato : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Data de Nascimento</label>
                            <input type="date" name="Nascimento" class="form-control" required value="{{isset($Registro->Nascimento) ? $Registro->Nascimento : ''}}">
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
                        <div class="col-sm-5">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                    </div>
                    <br>
                    @if(isset($Registro->id))
                    <div class="checkboxEscolas">
                        <div class="form-check escola">
                            {{-- <input type="hidden" name="Escola[]" value="{{isset($Registro->Escolas) && in_array($e->Nome,json_decode($Registro->Escolas,true)) ? $e->id : ''}}"> --}}
                            <input class="form-check-input" type="checkbox" value="1" name="credenciais" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                             Enviar Novas Credenciais de Login
                            </label>
                        </div>
                    </div>
                    @endif
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Auxiliares/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
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
        $("select[name=IDEscola]").on("change",function(){
            if($(this).val() == 0){
                $("select[name=Tipo]").find("optgroup[label=Escolas]").hide()
                $("select[name=Tipo]").find("optgroup[label=Secretaría]").show()
            }else{
                $("select[name=Tipo]").find("optgroup[label=Secretaría]").hide()
                $("select[name=Tipo]").find("optgroup[label=Escolas]").show()
            }
        })
        //
        $("input[name=CEP]").inputmask('99999-999')
        $("input[name=Celular]").inputmask('(99) 9 9999-9999')
    </script>
</x-educacional-layout>