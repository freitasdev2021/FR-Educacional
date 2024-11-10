<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
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
        <div class="fr-card-body dashboard">
            <div class="col-sm-12 row p-3">
                <div class="col-sm-6">
                   <div class="info-box">
                      <span class="info-box-icon bg-fr elevation-1"><i class='bx bx-list-check' ></i></span>
                      <div class="info-box-content">
                         <span class="info-box-text">Frequência</span>
                         <span class="info-box-number">  
                         {{$Boletim['Frequencia']}}/200
                         </span>
                      </div>
                   </div>
                </div>
                <div class="col-sm-6">
                    <div class="info-box">
                       <span class="info-box-icon bg-fr elevation-1" style="color:white;">%</span>
                       <div class="info-box-content">
                          <span class="info-box-text">Porcentagem de Frequência</span>
                          <span class="info-box-number">
                          @if(isset($Boletim['Boletim']['DadosAluno']))
                          {{$Boletim['PFrequencia']}}/{{$Boletim['Boletim']['DadosAluno']->MINFrequencia}}
                          @endif
                          </span>
                       </div>
                    </div>
                 </div>
             </div>
             <div class="col-sm-12">
                @if($Boletim['Boletim']['DadosAluno']->TPAvaliacao == "Nota")
                <table>
                    <x-headbimestre/>
                    <tbody>
                        @if(isset($Boletim['Boletim']['Disciplinas']))
                        @foreach($Boletim['Boletim']['Disciplinas'] as $b)
                        <tr>
                            <td>{{$b['Disciplina']}}</td>
                            <td>{{$b['Nota1B']}}</td>
                            <td>{{50-$b['Faltas1B']}}</td>
                            
                            <td>{{$b['Nota2B']}}</td>
                            <td>{{50-$b['Faltas2B']}}</td>
                            
                            <td>{{$b['Nota3B']}}</td>
                            <td>{{50-$b['Faltas3B']}}</td>
                            
                            <td>{{$b['Nota4B']}}</td>
                            <td>{{50-$b['Faltas4B']}}</td>
                            
                            <td>{{$Boletim['Boletim']['DadosAluno']->MediaPeriodo}}</td>
                            <td>{{($b['Nota1B'] + $b['Nota2B'] + $b['Nota3B'] + $b['Nota4B'] >= $Boletim['Boletim']['DadosAluno']->MediaPeriodo*4 && $Boletim['Boletim']['DadosAluno']->MINFrequencia >= $Boletim['PFrequencia']) ? 'Aprovado' : 'Reprovado'}}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                @else
                @foreach($conceitos as $registro)
                    <div>
                        <p><strong>Aluno:</strong> {{ $registro->nome }}</p>

                        <!-- Tabela de Respostas -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="content-cell">Conteúdo</th>
                                    <th class="response-cell">Resposta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(json_decode($registro->respostas, true) as $resposta)
                                    <tr>
                                        <td class="content-cell">{{$resposta['Conteudo']}}</td>
                                        <td class="response-cell">{{ $resposta['Resposta'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <br><hr><br> <!-- Espaço entre boletins -->
                    </div>
                @endforeach
                @endif
             </div>
        </div>
    </div>
</x-educacional-layout>