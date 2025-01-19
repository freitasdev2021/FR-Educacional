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
            table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
            }
            th, td {
                border: 1px solid black;
                padding: 8px;
            }
            th {
                background-color: #f2f2f2;
            }

            .inputNota{
                width:50px;
            }

            .inputTime{
                width:70px;
            }

            .inputTimeFinal{
                width:90px;
            }


            .tableHistorico{
                width:100%;
                overflow:scroll;
            }
            </style>
            <div class="row">
                <div class="col-auto">
                    <a href="{{route('Alunos/Historico/Abrir',$id)}}" class="btn btn-danger">Gerar Histórico Padrão</a>
                </div>
            </div>
            <hr>
            <form action="{{route('Alunos/GerarHistorico',$id)}}" method="POST" class="col-sm-12">
                @csrf
                @method("PATCH")
                <table class="table table-striped">
                    <thead>
                        <tr align="center">
                            <th colspan="5">ESTUDOS REALIZADOS</th>
                        </tr>
                        <tr>
                            <td>Série</td>
                            <td>Ano</td>
                            <td>Instituição</td>
                            <td>Município</td>
                            <td>Carga Horária Atual</td>
                        </tr>
                    </thead>
                    <tbody id="anosEstudados">
                        @foreach($AnosEstudados as $es)
                        <tr>
                            <td><input type="text" name="Serie[]" value="{{$es->Serie}}"></td>
                            <td><input type="text" name="Ano[]" value="{{$es->Ano}}" style="width:50px;"></td>
                            @if($es->Ano != "-")
                            <td><input type="text" name="Escola[]" value="{{$es->Escola}}" style="width:350px;"></td>
                            <td><input type="text" name="Cidade[]" value="{{$es->Cidade}}/{{$es->UF}}"></td>
                            <td><input type="time" name="CargaHoraria[]" value="{{$es->CargaHoraria}}"></td>
                            @else
                            <td><input type="text" name="Escola[]" value="-"></td>
                            <td><input type="text" name="Cidade[]" value="-"></td>
                            <td><input type="time" name="CargaHoraria[]" value="-"></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!--HISTÓRICO-->
                <div class="tableHistorico">
                    <table class="table table-striped">
                        <thead>
                            <tr align="center">
                                <th colspan="19">NOTAS/CARGA HORÁRIA</th>
                            </tr>
                            <tr>
                                <th rowspan="2">Áreas de Estudos</th>
                                <th colspan="18">Série / Anos / Períodos</th>
                            </tr>
                            <tr>
                               @foreach($series as $s)
                               <th class="serie">{{$s}}</th>
                               <th>CH</th>
                               @endforeach
                            </tr>
                        </thead>
                        <tbody id="queryHistorico">
                            @php
                                $cargaHorariaTotal = array_fill(1, 9, 0);
                            @endphp
                            @foreach($queryHistorico as $qh)
                                <tr class="trDisciplina">
                                    <td><input type="text" name="Disciplina[]" value="{{$qh->Disciplina}}"></td>
                                    @for($serie = 1; $serie <= 9; $serie++)
                                        @php
                                            $serieMarcada = false;
                                        @endphp
                                        @foreach($corpoHistorico as $np)
                                            @if($np['Disciplina'] == $qh->Disciplina && $np['Serie'] == "{$serie}º Ano")
                                                @if($np['RecAn'] > 0)
                                                    <td><input type='number' name="Nota[]" value="{{$np['RecAn']}}" class="inputNota"></td>
                                                @else
                                                    <td><input type='number' name="Nota[]" value="{{$np['Nota'] - $np['PontRec'] + $np['RecBim']}}" class="inputNota"></td>
                                                @endif
                                                <td><input type="time" name="CHDisciplina[]" class="inputTime" value="{{$np['CHDisciplina']}}"></td>
                                                @php
                                                    $serieMarcada = true;
                                                    $cargaHorariaTotal[$serie] += strtotime($np['CHDisciplina']) - strtotime('00:00:00');
                                                @endphp
                                                @break;
                                            @endif
                                        @endforeach
                                        @if(!$serieMarcada)
                                            <td><input type="number" name="Nota[]" class="inputNota"></td>
                                            <td><input type="time" name="CHDisciplina[]" class="inputTime"></td>
                                        @endif
                                    @endfor
                                </tr>
                            @endforeach
                            <tr>
                                <td>Carga Horária Total</td>
                                @for($serie = 1; $serie <= 9; $serie++)
                                    @if($cargaHorariaTotal[$serie] > 0)
                                    <td colspan="2"><input type="time" name="CHTotal[]" value="{{gmdate('H:i', $cargaHorariaTotal[$serie])}}" class="inputTimeFinal"></td>
                                    @else
                                    <td colspan="2"><input type="time" name="CHTotal[]" value="" class="inputTimeFinal"></td>
                                    @endif
                                @endfor
                            </tr>
                            <tr>
                                <td>Resultado Final</td>
                                @foreach($AnosEstudados as $qa)
                                    @if(is_numeric($qa->Ano))
                                    <td colspan="2"><input type="text" name="ResultadoFinal[]" value="{{\App\Http\Controllers\AlunosController::getResultadoAno($id,$qa->Ano)}}"></td>
                                    @else
                                    <td colspan="2"><input type="text" name="ResultadoFinal[]" value="" class="inputTimeFinal"></td>
                                    @endif
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--FIM HISTÓRICO-->
                <br>
                <button class="col-auto btn btn-danger" type="submit">Gerar</button>
            </form>
            <script>
                
            </script>
        </div>
    </div>
</x-educacional-layout>