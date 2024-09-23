<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                @if(Auth::user()->tipo == 2)
                <div class="col-auto">
                    <a href="{{route('Escolas/Disciplinas/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                @endif
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Escolas/Disciplinas/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Disciplina</th>
                        @if(Auth::user()->tipo == 2)<th style="text-align:center;" scope="col">Escolas</th>@endif
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