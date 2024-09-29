<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Relatorio</th>
                        <th style="text-align:center;" scope="col">Tipo</th>
                        <th style="text-align:center;" scope="col">Ultima Emissão</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Ocorrências</td>
                        <td>Quantitativo</td>
                        <td>28/09/2024</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/Imprimir','Ocorrencias')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Transferidos</td>
                        <td>Quantitativo</td>
                        <td>28/09/2024</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/Imprimir','Transferidos')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Remanejados</td>
                        <td>Quantitativo</td>
                        <td>28/09/2024</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/Imprimir','Remanejados')}}">Imprimir</a></td>
                      </tr>
                      <tr>
                        <td>Evadidos</td>
                        <td>Quantitativo</td>
                        <td>28/09/2024</td>
                        <td><a class="btn btn-xs btn-primary" href="{{route('Escolas/Relatorios/Imprimir','Evadidos')}}">Imprimir</a></td>
                      </tr>
                    </tbody>
                  </table>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>