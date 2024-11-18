<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--FILTROS-->
            <form action="{{route('Planejamentos/Metas')}}" method="GET">
                <div class="row">
                    <div class="col-sm-10">
                        <select name="IDEscola" class="form-control">
                            <option value="">Selecione a Escola</option>
                            @foreach($Escolas as $e)
                            <option value="{{$e->id}}">{{$e->Nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <input type="submit" class="form-control" value="Filtrar">
                    </div>
                </div>
            </form>
            <!--CORPO-->
            <form action="{{route('Planejamentos/Metas/Save',$IDEscola)}}" method="POST" class="row">
                @method('PATCH')
                @csrf
                <div class="col-sm-12">
                    <label>Marco Situacional</label>
                    <textarea name="MSituacional" class="form-control">{{isset($Registro) ? $Registro->MSituacional : ''}}</textarea>
                </div>
                <div class="col-sm-12">
                    <label>Marco Conceitual</label>
                    <textarea name="MConceitual" class="form-control">{{isset($Registro) ? $Registro->MConceitual : ''}}</textarea>
                </div>
                <div class="col-sm-12">
                    <label>Marco Operacional</label>
                    <textarea name="MOperacional" class="form-control">{{isset($Registro) ? $Registro->MOperacional : ''}}</textarea>
                </div>
                <div class="col-auto">
                    <br>
                    <button class="btn btn-fr">Salvar</button>
                </div>
            </form>
            <hr>
            <form action="{{route('Planejamentos/Objetivos/Save',$IDEscola)}}" method="POST" class="row">
                @method('PATCH')
                @csrf
                <div class="col-sm-3">
                    <label>Meta</label>
                    <input type="text" class="form-control" name="Meta">
                </div>
                <div class="col-sm-3">
                    <label>Meio para conseguir</label>
                    <input type="text" class="form-control" name="Meio">
                </div>
                <div class="col-sm-3">
                    <label>Data</label>
                    <input type="date" class="form-control" name="Data">
                </div>
                <div class="col-sm-3">
                    <label style="visibility:hidden;">Data</label>
                    <input type="submit" class="form-control" name="Enviar">
                </div>
            </form>
            <br>
            <div class="row">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Meta</th>
                            <th>Meio</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($Registro->MetasJSON)
                        @foreach(json_decode($Registro->MetasJSON) as $m)
                        <tr>
                            <td>{{$m->Meta}}</td>
                            <td>{{$m->Meio}}</td>
                            <td>{{$m->Data}}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!--------->
        </div>
    </div>
</x-educacional-layout>