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
            <form class="row" action="{{route('Alunos/Historico',$id)}}" method="GET">
                <div class="col-sm-10">
                    <select name="Modalidade" class="form-control">
                        <option value="E.FUNDAMENTAL" {{isset($_GET['Modalidade']) && $_GET['Modalidade'] == "E.FUNDAMENTAL" ? 'selected' : ''}}>E.FUNDAMENTAL</option>
                        <option value="E.INFANTIL" {{isset($_GET['Modalidade']) && $_GET['Modalidade'] == "E.INFANTIL" ? 'selected' : ''}}>E.INFANTIL</option>
                        <option value="CRECHE" {{isset($_GET['Modalidade']) && $_GET['Modalidade'] == "CRECHE" ? 'selected' : ''}}>CRECHE</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-default">Gerar</button>
                </div>
            </form>
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
                            <td><input type="text" name="CargaHoraria[]" value="{{$es->CargaHoraria}}"></td>
                            @else
                            <td><input type="text" name="Escola[]" value=""></td>
                            <td><input type="text" name="Cidade[]" value=""></td>
                            <td><input type="text" name="CargaHoraria[]" value=""></td>
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
                                <th colspan="{{($qtSerie*2)+1}}">NOTAS/CARGA HORÁRIA</th>
                            </tr>
                            <tr>
                                <th rowspan="2">Áreas de Estudos</th>
                                <th colspan="{{$qtSerie*2}}">Série / Anos / Períodos</th>
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
                                    <td style="display:flex;">
                                        <button type="button" class="btn btn-xs btn-danger btn-remove">X</button>&nbsp;
                                        <input type="text" name="Disciplina[]" value="{{$qh->Disciplina}}">
                                    </td>
                                    @for($serie = 1; $serie <= $qtSerie; $serie++)
                                        <input type="hidden" name="Serie[]" value="{{$serie}}º Ano">
                                        @php
                                            $serieMarcada = false;
                                        @endphp
                                        @foreach($corpoHistorico as $np)
                                            @if($np['Disciplina'] == $qh->Disciplina && $np['Serie'] == "{$serie}º Ano")
                                                @if($np['RecAn'] > 0)
                                                    <td><input type='text' data-ntdisciplina="{{$qh->Disciplina}}" data-serie='{{$serie}}º Ano' name="Nota[]" value="{{$np['RecAn']}}" class="inputNota"></td>
                                                @else
                                                    <td><input type='text' data-ntdisciplina="{{$qh->Disciplina}}"  data-serie='{{$serie}}º Ano' name="Nota[]" value="{{$np['Nota'] - $np['PontRec'] + $np['RecBim']}}" class="inputNota"></td>
                                                @endif
                                                <td><input type="text" data-chdisciplina="{{$qh->Disciplina}}"  data-serie='{{$serie}}º Ano' name="CHDisciplina[]" class="inputTime" value="{{number_format(\App\Http\Controllers\Controller::timeToNumber($np['CHDisciplina']),2,'.','')}}"></td>
                                                @php
                                                    $serieMarcada = true;
                                                    $cargaHorariaTotal[$serie] += \App\Http\Controllers\Controller::timeToNumber($np['CHDisciplina']);
                                                @endphp
                                                @break;
                                            @endif
                                        @endforeach
                                        @if(!$serieMarcada)
                                            <td><input type="text" data-ntdisciplina="{{$qh->Disciplina}}" value="-" data-serie='{{$serie}}º Ano' name="Nota[]" class="inputNota"></td>
                                            <td><input type="text" data-chdisciplina="{{$qh->Disciplina}}" value="-" data-serie='{{$serie}}º Ano' name="CHDisciplina[]" class="inputTime"></td>
                                        @endif
                                    @endfor
                                </tr>
                            @endforeach
                            <tr>
                                <td>Carga Horária Total</td>
                                @for($serie = 1; $serie <= $qtSerie; $serie++)
                                    @if($cargaHorariaTotal[$serie] > 0)
                                    <td colspan="2"><strong>{{number_format($cargaHorariaTotal[$serie], 2, '.', '')}}</strong></td>
                                    @else
                                    <td colspan="2"><strong>-</strong></td>
                                    @endif
                                @endfor
                            </tr>
                            <tr>
                                <td>Resultado Final</td>
                                @foreach($AnosEstudados as $qa)
                                    @if(is_numeric($qa->Ano))
                                    <td colspan="2"><input type="text" data-resdisciplina="" name="ResultadoFinal[]" value="-"></td>
                                    @else
                                    <td colspan="2"><input type="text" name="ResultadoFinal[]" value="" class="inputTimeFinal"></td>
                                    @endif
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                    <div class="col-sm-12">
                        <label>Observações</label>
                        <textarea name="Observacoes" class="form-control"></textarea>
                    </div>
                    <br>
                </div>
                <!--FIM HISTÓRICO-->
                <br>
                <input type="hidden" value="" name="Historico">
                <input type="hidden" name="Modalidade" value="{{$Modalidade}}">
                <button class="col-auto btn btn-primary" type="submit">Gerar</button>
                @if($Modalidade == "E.FUNDAMENTAL")
                <button class="col-auto btn btn-success" type="button" id="adicionarDisciplina">Adicionar Campo Disciplinar</button>
                @endif
            </form>
            <script>
                $("form").on("submit",function(){
                    let AnosEstudados = []
                    
                    $("#anosEstudados tr").each(function(){
                        AnosEstudados.push({
                            Serie : $("input[name='Serie[]']",this).val(),
                            Ano : $("input[name='Ano[]']",this).val(),
                            Escola : $("input[name='Escola[]']",this).val(),
                            Cidade : $("input[name='Cidade[]']",this).val(),
                            CargaHoraria : $("input[name='CargaHoraria[]']",this).val()
                        })
                    })
                    
                    let QueryHistorico = [];

                    $("#queryHistorico .trDisciplina").each(function () {
                        // Pega a disciplina
                        let disciplina = $("input[name='Disciplina[]']", this).val();

                        // Cria um array para as séries relacionadas à disciplina
                        let series = [];

                        // Itera sobre as séries existentes para preencher as notas e a carga horária
                        $("input[data-ntdisciplina]", this).each(function () {
                            let serie = $(this).data("serie");
                            let nota = $(this).val();
                            let cargaHoraria = $(`input[data-chdisciplina="${$(this).data("ntdisciplina")}"][data-serie="${serie}"]`).val();

                            series.push({
                                Serie: serie,
                                Nota: nota,
                                CHDisciplina: cargaHoraria,
                                Disciplina : disciplina
                            });
                        });

                        // Verifica se todas as séries estão representadas, e se não, adiciona com valores vazios ou os preenchidos
                        for (let serie = 1; serie <= {{$qtSerie}}; serie++) {
                            let serieExistente = series.find(s => s.Serie === `${serie}º Ano`);

                            // Se a série não existir, cria uma nova entrada com os valores preenchidos ou com o valor padrão
                            if (!serieExistente) {
                                // Pega os valores de nota e carga horária preenchidos (se houver)
                                let notaPreenchida = $(`input[data-ntdisciplina="${disciplina}"][data-serie="${serie}º Ano"]`).val() || '-';
                                let cargaHorariaPreenchida = $(`input[data-chdisciplina="${disciplina}"][data-serie="${serie}º Ano"]`).val() || '0';

                                series.push({
                                    Serie: `${serie}º Ano`,
                                    Nota: notaPreenchida,
                                    CHDisciplina: cargaHorariaPreenchida,
                                    Disciplina: disciplina
                                });
                            }
                            //

                            //
                        }

                        // Adiciona a disciplina e suas séries no array principal
                        QueryHistorico.push({
                            Disciplina: disciplina,
                            Serie: series
                        });
                    });

                    // Captura o ResultadoFinal[] e associa com as séries correspondentes
                    let ResultadoFinal = [];
                    $("input[name='ResultadoFinal[]']").each(function (index) {
                        let resultado = $(this).val();
                        ResultadoFinal.push({
                            Serie: `${index + 1}º Ano`,
                            Resultado: resultado
                        });
                    });

                    // Exibe o resultado no console
                    var enviarHistorico = {
                        AnosEstudados : AnosEstudados,
                        QueryHistorico : QueryHistorico,
                        ResultadoFinal : ResultadoFinal
                    }
                    
                    console.log(enviarHistorico);

                    $("input[name=Historico]").val(JSON.stringify(enviarHistorico))
                })

                $("#queryHistorico").on("click",".btn-remove",function(){
                    $(this).parents(".trDisciplina").remove()
                })

                var modalidade = $("input[name=Modalidade]").val()
                //ADICIONAR DISCIPLINA
                $("#adicionarDisciplina").on("click",function(){
                    //
                    $(".trDisciplina:last").after('<tr class="trDisciplina">\
                        <td style="display:flex;"><button type="button" class="btn btn-xs btn-danger btn-remove">X</button>&nbsp;<input type="text" name="Disciplina[]" value=""></td>\
                        <input type="hidden" name="Serie[]" value="1º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="1º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="1º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="2º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="2º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="2º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="3º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="3º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="3º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="4º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="4º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="4º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="5º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="5º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="5º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="6º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="6º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="6º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="7º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="7º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="7º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="8º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="8º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="8º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                        <input type="hidden" name="Serie[]" value="9º Ano">\
                        <td><input type="text" data-ntdisciplina="" value="-" data-serie="9º Ano" name="Nota[]" class="inputNota"></td>\
                        <td><input type="text" data-chdisciplina="" value="-" data-serie="9º Ano" name="CHDisciplina[]" class="inputTime"></td>\
                    </tr>');
                    //
                    $("input[name='Disciplina[]'").last().on("keyup",function(){
                        $(this).parents(".trDisciplina").find("input[name='Nota[]']").attr("data-ntdisciplina",$(this).val())
                        $(this).parents(".trDisciplina").find("input[name='CHDisciplina[]']").attr("data-chdisciplina",$(this).val())
                    })
                    //
                    })
                //
            </script>
        </div>
    </div>
</x-educacional-layout>