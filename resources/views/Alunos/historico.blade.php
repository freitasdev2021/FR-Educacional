<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            {{-- {{dd($historico)}} --}}
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f9f9f9;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header img {
                    width: 100px;
                    height: 100px;
                }
                .header h1 {
                    margin: 10px 0 5px;
                    font-size: 24px;
                    color: #333;
                }
                .header h2 {
                    margin: 5px 0;
                    font-size: 20px;
                    color: #666;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                table, th, td {
                    border: 1px solid #ccc;
                }
                th, td {
                    padding: 10px;
                    text-align: center;
                }
                th {
                    background-color: #f2f2f2;
                }
            </style>
            {{-- <table>
                <tr>
                    <th>Nome</th>
                    <td>João da Silva</td>
                </tr>
                <tr>
                    <th>Matrícula</th>
                    <td>2023123456</td>
                </tr>
                <tr>
                    <th>Data de Nascimento</th>
                    <td>15/03/2005</td>
                </tr>
            </table> --}}
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">Disciplina</th>
                        @foreach ($anos as $ano)
                            <th colspan="2">{{ $ano }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($anos as $ano)
                            <th>Nota</th>
                            <th>Carga Horária</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historico as $linha)
                        <tr>
                            <td>{{ $linha->Disciplina }}</td>
                            @foreach ($anos as $ano)
                                <td>{{ ($linha->{'RecAn_'.$ano} > 0) ? $linha->{'RecAn_'.$ano} : $linha->{'Total_' . $ano} - $linha->{'PontRec_'.$ano} + $linha->{'RecBim_'.$ano} }}</td>
                                <td>{{ $linha->{'CargaDisciplina_' . $ano} }} </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <ul>
                
            @foreach ($cargas as $kh => $linha)
                @foreach ($anos as $ano)
                    <li><strong>{{$ano}}</strong> - {{ $linha->{'CargaTotal_' . $ano} }} </li>
                @endforeach
            @endforeach
            </ul>
        </div>
    </div>
</x-educacional-layout>