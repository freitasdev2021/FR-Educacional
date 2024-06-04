<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <form class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Alunos/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                @if((Auth::user()->tipo == 2))
                <div class="col-auto">
                    <select name="" class="form-control">
                        <option value="">Selecione a Escola</option>
                        @foreach($Escolas as $es)
                        <option value="{{$es['id']}}">{{$es['Nome']}}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-auto">
                    <select name="" class="form-control">
                        <option value="">Selecione o Status</option>
                        <option value="0">Frequente</option>
                        <option value="1">Evadido</option>
                        <option value="2">Desistente</option>
                        <option value="3">Desligado</option>
                        <option value="4">Egresso</option>
                        <option value="5">Transferido Para Outra Rede</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="submit" value="Filtrar" class="form-control bg-fr text-white">
                </div>
            </form>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Alunos/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Turma</th>
                        @if(Auth::user()->tipo == 2)<th style="text-align:center;" scope="col">Escola</th>@endif
                        <th style="text-align:center;" scope="col">Serie</th>
                        <th style="text-align:center;" scope="col">Data de Nascimento</th>
                        <th style="text-align:center;" scope="col">Vencimento da Matrícula</th>
                        <th style="text-align:center;" scope="col">Situação</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>