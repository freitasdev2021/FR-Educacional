<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <style>
        table, th, td {
            border: 1px solid black;
        }

        .tableFicha{
            overflow:scroll;
        }
        </style>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <form class="row col-sm-12" method="GET">
                    <div class="col-auto">
                        <select name="IDTurma" class="form-control">
                            <option value="">Filtre pela turma</option>
                            @foreach($Turmas as $t)
                            <option value="{{$t->id}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->id ? 'selected' : ''}}>{{$t->Serie}} - {{$t->Nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="submit" class="btn btn-light form-control" value="Filtrar">
                    </div>
                </form>
            </div>
            <hr>
            <!--LISTAS-->
            <div class="col-sm-12">
                <!--LEGENDAS DAS SINTESES-->
                <div class="row">
                    @foreach($Fichas as $keyF => $f)
                    <div class="col-sm-2">
                        <table>
                            <thead>
                                <tr align="center">
                                    <th colspan="2">{{$keyF+1}} - {{$f->Disciplina}}</th>
                                </tr>
                                <tr>
                                    <th>Referência</th>
                                    <th>Síntese</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(json_decode($f->Sinteses) as $s)
                                <tr>
                                    <td>{{$s->Referencia}}</td>
                                    <td>{{$s->Sintese}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
                <hr>
                <!--LISTA DE ALUNOS-->
                <form action="{{route('Fichas/Avaliativa/Save')}}" class="col-sm-12 row" method="POST" id="formAvaliacao">
                    @csrf
                    <table class="tableFicha">
                        <thead>
                            <tr align="center">
                                <th>Aluno</th>
                                @foreach($Fichas as $keyF => $f)
                                <th style=" width:30px;" colspan="{{count(json_decode($f->Sinteses,true))}}">{{$keyF+1}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="sinteses">
                            @foreach($Alunos as $al)
                            <tr>
                                <td class="aluno">{{$al->Aluno}}</td>
                                @foreach($Fichas as $keyF => $f)
                                    @foreach(json_decode($f->Sinteses) as $fs)
                                        <td>{{$fs->Referencia}} - 
                                            <input name="Avaliacao[]" data-aluno="{{$al->Aluno}}" data-disciplina="{{$f->Disciplina}}" data-referencia="{{$fs->Referencia}}" data-sintese="{{$fs->Sintese}}" type="text" maxlength="2" style="width:30px;">
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="col-sm-12">
                        <hr>
                    </div>
                    <input type="hidden" name="EnviarSintese">
                    <input type="hidden" name="Fichas" value="{{json_encode($Fichas,JSON_UNESCAPED_UNICODE)}}">
                    <input type="hidden" name="IDTurma" value="{{isset($_GET['IDTurma']) ? $_GET['IDTurma'] : ''}}">
                    <button class="btn btn-success col-auto" type="submit">Salvar</button>
                </form>
                <!--FIM DA LISTA DE ALUNOS-->
            </div>
            <!--//-->
        </div>
        <script>
            $("#formAvaliacao").on("submit", function (e) {
        
                // Array para armazenar os dados
                var aval = [];
        
                // Iterar pelas linhas de alunos
                $("#sinteses tr").each(function () {
                    // Coletar o nome do aluno
                    var aluno = $(".aluno", this).text().trim();
        
                    // Objeto para armazenar as disciplinas e suas avaliações
                    var disciplinas = {};
        
                    // Iterar por cada input na linha do aluno
                    $("input", this).each(function () {
                        var disciplina = $(this).data("disciplina"); // Nome da disciplina
                        var referencia = $(this).data("referencia"); // Chave como "A", "B", "C", etc.
                        var valor = $(this).val(); // Valor da avaliação (Av, S, N, etc.)
        
                        // Garantir que a disciplina exista como uma chave no objeto
                        if (!disciplinas[disciplina]) {
                            disciplinas[disciplina] = [];
                        }
        
                        // Adicionar o objeto de avaliação (A, B, C) à disciplina
                        var objReferencia = {};
                        objReferencia[referencia] = valor;
                        disciplinas[disciplina].push(objReferencia);
                    });
        
                    // Adicionar o aluno e suas disciplinas ao array principal
                    aval.push({
                        Aluno: aluno,
                        Disciplinas: disciplinas
                    });
                });
        
                // Console para verificar o resultado
                $("input[name=EnviarSintese]").val(JSON.stringify(aval))
            });
        </script>                      
    </div>
</x-educacional-layout>