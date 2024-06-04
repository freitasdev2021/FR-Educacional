<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            @if(Auth::user()->tipo == 2)
            <hr>
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Escolas/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            @endif
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                @if(Auth::user()->tipo == 2)
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Escolas/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Endereço</th>
                        <th style="text-align:center;" scope="col">Email</th>
                        <th style="text-align:center;" scope="col">Telefone</th>
                        <th style="text-align:center;" scope="col">Vagas</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
                @else
                <form action="{{route('Escolas/Save')}}" method="POST">
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
                    @endif
                    <input type="hidden" name="IDOrg" value="{{Auth::user()->id_org}}">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome da escola</label>
                            <input type="text" name="Nome" class="form-control @error('Organizacao') is-invalid @enderror" maxlength="100" required value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Quantidade de Vagas</label>
                            <input type="text" name="QTVagas" class="form-control" value="{{isset($Registro->QTVagas) ? $Registro->QTVagas : ''}}" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Telefone</label>
                            <input type="text" name="Telefone" class="form-control" value="{{isset($Registro->Telefone) ? $Registro->Telefone : ''}}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>E-mail</label>
                            <input type="email" name="Email" class="form-control" maxlength="50" required value="{{isset($Registro->Email) ? $Registro->Email : ''}}" disabled>
                        </div>
                        <div class="col-sm-3">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" maxlength="9" minlength="9" required value="{{isset($Registro->CEP) ? $Registro->CEP : ''}}" disabled>
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" required value="{{isset($Registro->Rua) ? $Registro->Rua : ''}}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="60" required value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" disabled>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="60" value="{{isset($Registro->Bairro) ? $Registro->Bairro : ''}}" required disabled>
                        </div>
                        <div class="col-sm-2">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->UF) ? $Registro->UF : ''}}" minlength="2" required disabled>
                        </div>
                        <div class="col-sm-2">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Registro->Numero) ? $Registro->Numero : ''}}" required disabled>
                        </div>
                    </div>
                </form> 
                @endif
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>