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
                <form action="{{route('Alunos/NEE/Save')}}" method="POST" enctype="multipart/form-data">
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
                    @if(isset($Registro->id))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    <input type="hidden" name="oldLaudo" value="{{$Registro->Laudo}}">
                    @endif
                    <input type="hidden" name="IDAluno" value="{{$IDAluno}}">
                    <input type="hidden" name="CDPasta" value="{{$CDPasta}}">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Necessidade</label>
                            <input type="text" name="DSNecessidade" class="form-control" value="{{isset($Registro->DSNecessidade) ? $Registro->DSNecessidade : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição da Necessidade</label>
                            <textarea name="DSAcompanhamento" class="form-control">{{isset($Registro->DSAcompanhamento) ? $Registro->DSAcompanhamento : ''}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>CID</label>
                            <input type="text" maxlength="10" name="CID" class="form-control" value="{{isset($Registro->CID) ? $Registro->CID : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Anexo do Laudo</label>
                            <input type="file" name="Laudo" class="form-control" maxlength="50">
                        </div>
                        <div class="col-sm-4">
                            <label>Data do Laudo</label>
                            <input type="date" name="DTLaudo" class="form-control" maxlength="50" required value="{{isset($Registro->DTLaudo) ? $Registro->DTLaudo : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Alunos/NEE',$IDAluno)}}">Voltar</a>
                    </div>
                </form>   
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>