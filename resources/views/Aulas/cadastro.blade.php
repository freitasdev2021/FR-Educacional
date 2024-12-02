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
                <form action="{{route('Aulas/Save')}}" method="POST">
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
                        <div class="col-sm-4">
                            <label>Data</label>
                            <input type="date" name="DTAula" class="form-control" value="{{isset($Registro->DTAula) ? $Registro->DTAula : ''}}" >
                        </div>
                        <div class="col-sm-2">
                            <label>Etapa</label>
                            <select name="Estagio" class="form-control">
                                <optgroup label="Bimestre">
                                    <option value="1º BIM" {{isset($Registro) && $Registro->Estagio == "1º BIM" ? 'selected' : ''}}>1º Bimestre</option>
                                    <option value="2º BIM" {{isset($Registro) && $Registro->Estagio == "2º BIM" ? 'selected' : ''}}>2º Bimestre</option>
                                    <option value="3º BIM" {{isset($Registro) && $Registro->Estagio == "3º BIM" ? 'selected' : ''}}>3º Bimestre</option>
                                    <option value="4º BIM" {{isset($Registro) && $Registro->Estagio == "4º BIM" ? 'selected' : ''}}>4º Bimestre</option>
                                </optgroup>
                                
                                <optgroup label="Trimestre">
                                    <option value="1º TRI" {{isset($Registro) && $Registro->Estagio == "1º TRI" ? 'selected' : ''}}>1º Trimestre</option>
                                    <option value="2º TRI" {{isset($Registro) && $Registro->Estagio == "2º TRI" ? 'selected' : ''}}>2º Trimestre</option>
                                    <option value="3º TRI" {{isset($Registro) && $Registro->Estagio == "3º TRI" ? 'selected' : ''}}>3º Trimestre</option>
                                </optgroup>
                                
                                <optgroup label="Semestre">
                                    <option value="1º SEM" {{isset($Registro) && $Registro->Estagio == "1º SEM" ? 'selected' : ''}}>1º Semestre</option>
                                    <option value="2º SEM" {{isset($Registro) && $Registro->Estagio == "2º SEM" ? 'selected' : ''}}>2º Semestre</option>
                                </optgroup>
                                
                                <optgroup label="Periodo">
                                    <option value="1º PER" {{isset($Registro) && $Registro->Estagio == "1º PER" ? 'selected' : ''}}>1º Período</option>
                                </optgroup>
                            </select>                            
                        </div>
                        <div class="col-sm-{{(Auth::user()->tipo == 6) ? '6' : '3'}}">
                            <label>Turma</label>
                            <select class="form-control" name="IDTurma">
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->id}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->id ? 'selected' : ''}}>{{$t->Nome}} - {{$t->Serie}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="TPConteudo" value="0">
                        @if(in_array(Auth::user()->tipo,[4,5,4.5,5.5]))
                        <div class="col-sm-2">
                            <label>Professor</label>
                            <select class="form-control" name="IDProfessor">
                                <option value="">Selecione</option>
                                
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição</label>
                            <textarea name="DSConteudo" class="form-control">{{(isset($Registro->DSConteudo)) ? $Registro->DSConteudo : ''}}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 row disciplinas">

                    </div>
                    <br>
                    @if(!$id)
                    <div class="col-sm-12 p-2">
                        <div>
                            <input type="checkbox" name="todosPresentes">Todos Presentes
                        </div>
                        <br>
                        <table class="table table-sm tabela">
                            <thead>
                              <tr>
                                <th style="text-align:center;" scope="col">Aluno</th>
                                <th style="text-align:center;" scope="col">Presente</th>
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
                        <a class="btn btn-light col-auto" href="{{route('Aulas/index')}}">Voltar</a>
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
                          url : "/Professores/DisciplinasProfessor/"+$(this).val()
                       }).done(function(response){
                          $(".disciplinas").html(response)
                       })

                       $.ajax({
                          method : 'GET',
                          url : "/Aulas/ListaAlunos/"+$(this).val()+"/"+$("input[name=DTAula]").val()
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
                          url : "/Professores/DisciplinasProfessor/"+$("select[name=IDTurma]").val()+"/"+$(this).val()
                       }).done(function(response){
                            if(!IDAula){
                                $(".disciplinas").html(response)
                            }
                       })

                       $.ajax({
                          method : 'GET',
                          url : "/Aulas/ListaAlunos/"+$("select[name=IDTurma]").val()+"/"+$("input[name=DTAula]").val()
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
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>