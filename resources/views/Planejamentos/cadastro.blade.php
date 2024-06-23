<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <!--CABECALHO-->
          <form action="" method="POST">
            @csrf
            <div class="row">
               <div class="col-sm-6">
                  <label>Planejamento</label>
                  <input type="text" name="NMPlanejamento" class="form-control">
               </div>
               <div class="col-sm-6">
                  <label>Disciplina</label>
                  <select name="IDDisciplina" class="form-control">
                     <option value="">Selecione</option>
                     @foreach($Disciplinas as $d)
                     <option value="{{$d['IDDisciplina']}}">{{$d['Disciplina']}}</option>
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
                                                
                     </tbody>
                  </table>
               </div>
            </div>
          </form>
          <script>
            $("select[name=IDDisciplina]").on("change",function(){
               $.ajax({
                  method : 'GET',
                  url : "/Escolas/Turmas/"+$(this).val()+"/getTurmasDisciplina/HTML"
               }).done(function(response){
                  $("#turmasTable").html(response)
               })
            })
          </script>
          <!--//-->
       </div>
    </div>
 </x-educacional-layout>