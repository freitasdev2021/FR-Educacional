<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
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
                @switch($Periodo)
                    @case('Bimestral')
                        <x-headbimestre/>
                        @break
                    @case('Trimestral')
                        <x-headtrimestre/>
                        @break
                    @case('Semestral')
                        <x-headsemestre/>
                        @break
                    @case('Anual')
                        <x-headanual/>
                        @break
                    @default
                        
                @endswitch
                <tbody>
                    @switch($Periodo)
                        @case('Bimestral')
                            @foreach($Boletim as $b)
                            <tr>
                                <td>{{$b->Disciplina}}</td>
                                <td>{{$b->Nota1B}}</td>
                                <td>{{$b->Faltas1B}}</td>
                                <td>{{$b->Rec1B}}</td>
                                <td>{{$b->Nota2B}}</td>
                                <td>{{$b->Faltas2B}}</td>
                                <td>{{$b->Rec2B}}</td>
                                <td>{{$b->Nota3B}}</td>
                                <td>{{$b->Faltas3B}}</td>
                                <td>{{$b->Rec3B}}</td>
                                <td>{{$b->Nota4B}}</td>
                                <td>{{$b->Faltas4B}}</td>
                                <td>{{$b->Rec4B}}</td>
                                <td>{{$MediaPeriodo}}</td>
                                <td>{{($b->Nota1B + $b->Nota2B + $b->Nota3B + $b->Nota4B >= $MediaPeriodo*4) ? 'Aprovado' : 'Reprovado'}}</td>
                            </tr>
                            @endforeach
                        @break
                    @case('Trimestral')
                        @foreach($Boletim as $b)
                            <tr>
                                <td>{{$b->Disciplina}}</td>
                                <td>{{$b->Nota1B}}</td>
                                <td>{{$b->Faltas1B}}</td>
                                <td>{{$b->Rec1B}}</td>
                                <td>{{$b->Nota2B}}</td>
                                <td>{{$b->Faltas2B}}</td>
                                <td>{{$b->Rec2B}}</td>
                                <td>{{$b->Nota3B}}</td>
                                <td>{{$b->Faltas3B}}</td>
                                <td>{{$b->Rec3B}}</td>
                                <td>{{$MediaPeriodo}}</td>
                                <td>{{($b->Nota1B + $b->Nota2B + $b->Nota3B >= $MediaPeriodo*3) ? 'Aprovado' : 'Reprovado'}}</td>
                            </tr>
                            @endforeach
                        @break
                    @case('Semestral')
                        @foreach($Boletim as $b)
                            <tr>
                                <td>{{$b->Disciplina}}</td>
                                <td>{{$b->Nota1B}}</td>
                                <td>{{$b->Faltas1B}}</td>
                                <td>{{$b->Rec1B}}</td>
                                <td>{{$b->Nota2B}}</td>
                                <td>{{$b->Faltas2B}}</td>
                                <td>{{$b->Rec2B}}</td>
                                <td>{{$MediaPeriodo}}</td>
                                <td>{{($b->Nota1B + $b->Nota2B >= $MediaPeriodo*3) ? 'Aprovado' : 'Reprovado'}}</td>
                            </tr>
                            @endforeach
                        @break
                    @case('Anual')
                        @foreach($Boletim as $b)
                            <tr>
                                <td>{{$b->Disciplina}}</td>
                                <td>{{$b->Nota1B}}</td>
                                <td>{{$b->Rec1B}}</td>
                                <td>{{$MediaPeriodo}}</td>
                                <td>{{($b->Nota1B >= $MediaPeriodo*3) ? 'Aprovado' : 'Reprovado'}}</td>
                            </tr>
                            @endforeach
                        @break
                    @default
                    @endswitch
                </tbody>
            </table>
        </div>
    </div>
</x-educacional-layout>