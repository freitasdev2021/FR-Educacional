<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Save')}}" method="POST">
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
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Nome completo do Aluno</label>
                            <input type="text" class="form-control" value=""> 
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Aluno</label>
                            <input type="email" class="form-control" value=""> 
                        </div>
                        <div class="col-sm-3">
                            <label>CPF do Aluno</label>
                            <input type="email" class="form-control" value=""> 
                        </div>
                        <div class="col-sm-3">
                            <label>Email do Aluno</label>
                            <input type="email" class="form-control" value=""> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                        <label>Nome completo do Responsavel</label>
                        <input type="text" class="form-control" value=""> 
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Responsavel</label>
                            <input type="text" class="form-control" value=""> 
                        </div>
                        <div class="col-sm-3">
                            <label>CPF do Responsavel</label>
                            <input type="text" class="form-control" value=""> 
                        </div>
                        <div class="col-sm-3">
                        <label>Email do Responsavel</label>
                        <input type="email" class="form-control" value=""> 
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
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>RG dos Pais</label>
                            <input type="file" class="form-control">
                        </div>
                        <div class="col-sm-4">
                            <label>RG do Aluno</label>
                            <input type="file" class="form-control">
                        </div>
                        <div class="col-sm-4">
                            <label>Comprovante de Residência</label>
                            <input type="file" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Possui NEE</label>
                            <select class="form-control">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Tem Alergía?</label>
                            <select class="form-control">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control">
                                <option value="">Selecione</option>
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
</x-educacional-layout>