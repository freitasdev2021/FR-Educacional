<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <!--CABECALHO-->
          <form action="{{route('Planejamentos/Save')}}" method="POST">
            {{-- <pre>
               {{print_r($Turmas)}}
            </pre> --}}
            @csrf
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
            @if(isset($id))
            <input type="hidden" name="id" value="{{$id}}">
            @endif
            <div class="row">
               <div class="col-sm-6">
                  <label>Planejamento</label>
                  <input type="text" name="NMPlanejamento" class="form-control" value="{{isset($Registro) ? $Registro->NMPlanejamento : ''}}" {{($id) ? 'disabled' : 'required'}}>
               </div>
               <div class="col-sm-6">
                  <label>Disciplina</label>
                  <select name="IDDisciplina" class="form-control" {{($id) ? 'disabled' : 'required'}}>
                     <option value="">Selecione</option>
                     @foreach($Disciplinas as $d)
                        <option value="{{$d->IDDisciplina}}" {{(isset($Registro->IDDisciplina) && $d->IDDisciplina == $Registro->IDDisciplina) ? 'selected' : ''}}>{{$d->Disciplina}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="col-sm-12">
                  <br>
                  <table class="table">
                     <thead class="bg-fr text-white">
                        <th></th>
                        <th>Turma</th>
                        <th>Serie</th>
                        <th>Escola</th>
                     </thead>
                     <tbody id="turmasTable">
                        @if($id)
                           @foreach($Turmas as $t)
                              <tr>
                                 <td><input type="checkbox" value="{{$t->IDTurma}}" {{$t->Checked}} name="Turma[]"></td>
                                 <td>{{$t->Turma}}</td>
                                 <td>{{$t->Serie}}</td>
                                 <td>{{$t->Escola}}</td>
                              </tr>
                           @endforeach
                        @endif
                     </tbody>
                  </table>
               </div>
            </div>
            <br>
            <div class="col-sm-12 text-left row">
               <button type="submit" class="btn btn-fr col-auto">Salvar</button>
               &nbsp;
               <a class="btn btn-light col-auto" href="{{route('Planejamentos/index')}}">Voltar</a>
            </div>
          </form>
          <script>
            $("select[name=IDDisciplina]").on("change",function(){
               $.ajax({
                  method : 'GET',
                  url : "/Escolas/Turmas/"+$(this).val()+"/getTurmasDisciplina/HTML/"{{$id}}
               }).done(function(response){
                  $("#turmasTable").html(response)
               })
            })
          </script>
          <!--//-->
       </div>
    </div>
 </x-educacional-layout>