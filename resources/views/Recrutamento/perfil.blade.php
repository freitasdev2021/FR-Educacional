<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Candidatura/Save')}}" method="POST">
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
            <div class="col-sm-12 p-2">
               <div class="row">
                   <div class="col-sm-4">
                     <label>Nome</label>
                     <input type="text" class="form-control" name="Nome" value="{{isset($Registro) ? $Registro->Nome : ''}}">
                   </div>
                   <div class="col-sm-4">
                    <label>Email</label>
                    <input type="email" class="form-control" name="Email" value="{{isset($Registro) ? $Registro->Email : ''}}">
                  </div>
                  <div class="col-sm-4">
                    <label>Escolaridade</label>
                    <select name="Escolaridade" class="form-control">
                        <option value="">Escolaridade</option>
                        <option value="Ensino Fundamental I" {{isset($Registro) && $Registro->Escolaridade == "Ensino Fundamental I" ? 'selected' : ''}}>Ensino Fundamental I</option>
                        <option value="Ensino Fundamental II" {{isset($Registro) && $Registro->Escolaridade == "Ensino Fundamental II" ? 'selected' : ''}}>Ensino Fundamental II</option>
                        <option value="Ensino Médio" {{isset($Registro) && $Registro->Escolaridade == "Ensino Médio" ? 'selected' : ''}}>Ensino Médio</option>
                        <option value="Ensino Técnico" {{isset($Registro) && $Registro->Escolaridade == "Ensino Técnico" ? 'selected' : ''}}>Ensino Técnico</option>
                        <option value="Ensino Superior" {{isset($Registro) && $Registro->Escolaridade == "Ensino Superior" ? 'selected' : ''}}>Ensino Superior</option>
                    </select>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-12">
                    <label>Descreva sua experiência</label>
                    <textarea name="DSCandidato" class="form-control">{{isset($Registro) ? $Registro->DSCandidato : ''}}</textarea>
                  </div>
               </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Atualizar Currículo</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('dashboard')}}">Voltar</a>
                   </div>
                </div>
             </div>
        </form>
        <hr>
        <div class="row">
         <div class="col-sm-6">
            <h5 align="center">Anexos</h5>
            <form action="{{route('Candidatura/Anexo/Save')}}" method="POST" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="IDCandidato" value="{{$Registro->id}}">
               <div class="row">
                  <div class="col-sm-6">
                     <label>Anexo</label>
                     <input type="file" name="Anexo" class="form-control">
                  </div>
                  <div class="col-sm-6">
                     <label>Tipo</label>
                     <select name="Tipo" class="form-control">
                        <option value="">Selecione</option>
                        <option value="Documento">Documento</option>
                        <option value="Certificado">Certificado</option>
                     </select>
                  </div>
               </div>
               <br>
               @if($Anexos)
               <table class="table table-striped">
                  <thead>
                     <tr>
                        <th>Anexo</th>
                        <th>Tipo</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($Anexos as $a)
                     <tr>
                        <td>{{$a->Anexo}}</td>
                        <td>{{$a->Tipo}}</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @endif
               <hr>
               <div class="col-auto">
                  <button class="btn btn-fr">Adicionar</button>
               </div>
            </form>
         </div>
         <div class="col-sm-6">
            <h5 align="center">Cursos</h5>
            <form action="{{route('Candidatura/Curso/Save')}}" method="POST">
               @csrf
               <input type="hidden" name="IDCandidato" value="{{$Registro->id}}">
               <div class="row">
                  <div class="col-sm-6">
                     <label>Nome</label>
                     <input type="text" name="Nome" class="form-control">
                  </div>
                  <div class="col-sm-6">
                     <label>Tipo</label>
                     <select name="Tipo" class="form-control">
                        <option value="">Tipo do Curso</option>
                        <option value="Superior">Superior</option>
                        <option value="Técnico">Técnico</option>
                        <option value="Profissionalizante">Profissionalizante</option>
                        <option value="Pós-Graduação">Pós-Graduação</option>
                        <option value="Mestrado">Mestrado</option>
                        <option value="Doutorado">Doutorado</option>
                        <option value="PHD">PHD</option>
                        <option value="MBA">MBA</option>
                     </select>
                  </div>
               </div>
               <div class="row">
                <div class="col-sm-6">
                    <label>Início</label>
                    <input type="date" class="form-control" name="INICurso">
                </div>
                <div class="col-sm-6">
                    <label>Término</label>
                    <input type="date" class="form-control" name="TERCurso">
                </div>
               </div>
               <br>
               @if($Cursos)
               <table class="table table-striped">
                  <thead>
                     <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Início</th>
                        <th>Término</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($Cursos as $c)
                     <tr>
                        <td>{{$c->Nome}}</td>
                        <td>{{$c->Tipo}}</td>
                        <td>{{$c->INICurso}}</td>
                        <td>{{$c->TERCurso}}</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @endif
               <hr>
               <div class="col-auto">
                  <button class="btn btn-fr">Adicionar</button>
               </div>
            </form>
         </div>
        </div>
       </div>
    </div>
 </x-educacional-layout>