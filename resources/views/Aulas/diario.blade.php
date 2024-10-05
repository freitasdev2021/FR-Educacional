<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <!--LISTAS-->
          <form action="{{route(Route::currentRouteName())}}" method="GET" class="row">
            <div class="col-sm-2">
                <label>Professor</label>
                <select name="Professor" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($ProfessoresComentario as $prof)
                    <option value="{{$prof['id']}}" {{isset($_GET['Professor']) && $_GET['Professor'] == $prof['id'] ? 'selected' : ''}}>{{$prof['Nome']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label>Estágio</label>
                <select name="Estagio" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($Estagio as $e)
                    <option value="{{$e}}"  {{isset($_GET['Estagio']) && $_GET['Estagio'] == $e ? 'selected' : ''}}>{{$e}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label>Data da Aula</label>
                <select name="Data" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($Data as $dt)
                    <option value="{{$dt}}" {{isset($_GET['Data']) && $_GET['Data'] == $dt ? 'selected' : ''}}>{{date('d/m/Y', strtotime($dt))}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label>Comentário (Para Relatório)</label>
                <select name="Comentario" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($ComentariosProfessor as $cp)
                    <option value="{{$cp->id}}" {{isset($_GET['Comentario']) && $_GET['Comentario'] == $cp->id ? 'selected' : ''}}>{{$cp->Titulo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label style="visibility:hidden">a</label>
                <input type="submit" class="btn bg-fr text-white form-control" value="Filtrar">
            </div>
            @if(isset($_GET['Professor']) && !empty($_GET['Professor']) && isset($_GET['Estagio']) && !empty($_GET['Estagio']) && isset($_GET['Data']) && !empty($_GET['Data']) && isset($_GET['Comentario']) && !empty($_GET['Comentario']))
            <div class="col-auto">
                <label style="visibility:hidden">a</label>
                <a href="{{route("Aulas/Diario/Exportar",['Professor'=>$_GET['Professor'],"Estagio"=>$_GET['Estagio'],"Data"=>$_GET['Data'],"Comentario"=>$_GET['Comentario']])}}" class="btn bg-fr text-white form-control">Gerar Relatório</a>
            </div>
            @endif
          </form>
          <hr>
          @foreach($relatorios as $r)
          <div class="card">
             <div class="card-header bg-fr text-white">
                {{$r->Professor}} - {{$r->Turma." (".$r->Serie.")"}} - {{date('d/m/Y', strtotime($r->created_at))}} - {{$r->INIAula}} - {{$r->TERAula}}
             </div>
             <div class="card-body">
                {{-- <h5 class="card-title">{{$r->Aula}}</h5> --}}
                @if($r->conteudoLecionado)
                @foreach(json_decode($r->conteudoLecionado) as $cl)
                <div class="card">
                    <div class="card-body">
                       <h5 class="card-title">{{$cl->Atividade}}</h5>
                       <br>
                       <p class="card-text">{{$cl->Conteudo}}</p>
                    </div>
                 </div>
                @endforeach
                @endif
             </div>
          </div>
          @endforeach
          <!--COMENTARIOS-->
          <h5>Comentários</h5>
          <form class="row" method="POST" action="{{route('Aulas/Diario/Comentar')}}">
            @csrf
            <div class="col-sm-12">
                <label>Professor</label>
                <select name="IDProfessor" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($ProfessoresComentario as $prof)
                    <option value="{{$prof['id']}}">{{$prof['Nome']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12">
                <label>Titulo</label>
                <input type="text" name="Titulo" class="form-control">
            </div>
            <div class="col-sm-12">
                <label>Comentário</label>
                <textarea name="Comentario" class="form-control"></textarea>
            </div>
            <div class="col-sm-4">
                <br>
                <button type="submit" class="btn btn-success">Comentar</button>
            </div>
            <div class="col-sm-12">
                <br>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Professor</th>
                            <th>Titulo</th>
                            <th>Comentário</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ComentariosProfessor as $cp)
                        <tr>
                            <td>{{$cp->Professor}}</td>
                            <td>{{$cp->Titulo}}</td>
                            <td>{{$cp->Comentario}}</td>
                            <td>{{date('d/m/Y', strtotime($cp->created_at))}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
          <!--FIM DOS COMENTARIOS-->
        </div>
    </div>
 </x-educacional-layout>