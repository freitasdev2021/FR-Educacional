<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Aulas/Avaliacoes/Save')}}" method="POST">
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
                    @if(!empty($id))
                    <input type="hidden" name="id" value="{{$id}}">
                    @endif
                    <input type="hidden" name="TPAula" value="Normal">
                    <input type="hidden" name="IDOrg" value="{{Auth::user()->id_org}}">
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Etapa</label>
                            <select name="Estagio" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Periodos as $p)
                                <option value="{{$p['Periodo']}}" data-ini="{{$p['DTInicio']}}" data-ter="{{$p['DTTermino']}}">{{$p['Periodo']}} ({{date('d/m/Y',strtotime($p['DTInicio']))}} - {{date('d/m/Y',strtotime($p['DTTermino']))}})</option>
                                @endforeach
                            </select>                            
                        </div>
                        <div class="col-sm-4">
                            <label>Data</label>
                            <select name="DTAula" class="form-control">
                                @foreach($Datas as $d)
                                <option value="{{$d}}">{{date('d/m/Y',strtotime($d))}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-{{(Auth::user()->tipo == 6) ? '3' : '3'}}">
                            <label>Turma</label>
                            <select class="form-control" name="IDTurma">
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->id}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->id ? 'selected' : ''}}>{{$t->Nome}} - {{$t->Serie}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(in_array(Auth::user()->tipo,[4,5,4.5,5.5]))
                        <div class="col-sm-2">
                            <label>Professor</label>
                            <select class="form-control" name="IDProfessor">
                                
                            </select>
                        </div>
                        @endif
                        <div class="col-sm-3">
                            <label>Pontuacao/Conceito</label>
                            <input type="text" name="Pontuacao" class="form-control">
                        </div>
                        <div class="col-sm-3">
                            <label>Disciplina/Campo de Experiência</label>
                            <select class="form-control" name="IDDisciplina">

                            </select>
                        </div>
                        <div class="col-sm-9">
                            <label>Descrição</label>
                            <input type="text" name="DSConteudo" class="form-control" value="{{(isset($Registro->DSConteudo)) ? $Registro->DSConteudo : ''}}">
                        </div>
                        <input type="hidden" name="TPConteudo" value="1">
                    </div>
                    <br>
                    <div class="col-sm-12 row disciplinas">

                    </div>
                    <br>
                    @if(!$id)
                    <div class="col-sm-12 p-2">
                        <table class="table table-sm tabela">
                            <thead>
                              <tr>
                                <th style="text-align:center;" scope="col">Aluno</th>
                                <th style="text-align:center;" scope="col">Nota</th>
                              </tr>
                            </thead>
                            <tbody id="presencas">
                              
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    @endif
                    <div class="col-sm-12 text-left row">
                        @if(!isset($Registro->STAula) || $Registro->STAula < 2)
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        @endif
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Aulas/Avaliacoes/index')}}">Voltar</a>
                    </div>
                </form>
                @if(Auth::user()->tipo == 6)
                <script>
                    $("input[name=todosPresentes]").on("change",function(){
                        $('input[name="Chamada[]"]').prop('checked', $(this).prop('checked'));
                    })
                    //SELECIONA AS DISCIPLINAS
                    
                    $("select[name=IDTurma]").on("change",function(){
                       $.ajax({
                          method : 'GET',
                          url : "/Professores/SelectDisciplinasProfessor/"+$(this).val()
                       }).done(function(response){
                          $("select[name=IDDisciplina]").html(response)
                       })

                       $.ajax({
                          method : 'GET',
                          url : "/Aulas/ListaAlunosAvaliacao/"+$(this).val()+"/"+$("select[name=DTAula]").val()
                       }).done(function(alun){
                        //console.log(alun)
                          $("#presencas").html(alun)
                       })

                    })
                </script> 
                @else
                <script>
                    $("input[name=todosPresentes]").on("change",function(){
                        $('input[name="Chamada[]"]').prop('checked', $(this).prop('checked'));
                    })
                    //SELECIONA AS DISCIPLINAS
                    
                    $("select[name=IDProfessor]").on("change",function(){
                        IDAula = "{{$id}}";
                        //alert($(this).val())
                       $.ajax({
                          method : 'GET',
                          url : "/Professores/SelectDisciplinasProfessor/"+$("select[name=IDTurma]").val()+"/"+$(this).val()
                       }).done(function(response){
                            if(!IDAula){
                                $("select[name=IDDisciplina]").html(response)
                            }
                       })

                       $.ajax({
                          method : 'GET',
                          url : "/Aulas/ListaAlunosAvaliacao/"+$("select[name=IDTurma]").val()+"/"+$("select[name=DTAula]").val()
                       }).done(function(alun){
                        //console.log(alun)
                          $("#presencas").html(alun)
                       })

                    })

                    $("select[name=IDTurma]").on("change",function(){
                        $.ajax({
                          method : 'GET',
                          url : "/Professores/Turmas/"+$(this).val()
                       }).done(function(turmas){
                          $("select[name=IDProfessor]").html(turmas)
                       })
                    })
                </script> 
                @endif  
                @if(!empty($id) && Auth::user()->tipo!=6)
                <script>
                    var IDProfessor = "{{ $Registro->IDProfessor }}"
                    $.ajax({
                        method : 'GET',
                        url : "/Professores/Turmas/"+$("select[name=IDTurma]").val()
                    }).done(function(turmas){
                        $("select[name=IDProfessor]").html(turmas)
                        $("select[name=IDProfessor]").val(IDProfessor)
                    })
                </script>
                @endif
                <script>
                    $("select[name=Estagio]").on("change", function() {
                        var ini = $("option:selected", this).attr("data-ini");
                        var ter = $("option:selected", this).attr("data-ter");

                        $("select[name=DTAula] option").each(function() {
                            var valor = $(this).attr("value");

                            // Verifica se o valor está dentro do intervalo
                            if (valor >= ini && valor <= ter) {
                                $(this).show();  // Mostra a opção válida
                            } else {
                                $(this).hide();  // Oculta a opção fora do intervalo
                            }
                        });
                    });
                </script>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>