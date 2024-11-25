<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDAluno)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('Alunos/Anexos/Save')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="CDPasta" value="{{$CDPasta}}">
                        <input type="hidden" name="IDAluno" value="{{$IDAluno}}">
                        <label>Anexo</label>
                        <input type="file" name="Anexo" class="form-control">
                        <br>
                        <label>Descrição do Anexo</label>
                        <textarea name="DSAnexo" class="form-control"></textarea>
                        <br>
                        <button type="submit" class="btn btn-success">Anexar</button>
                    </form>
                </div>
            </div>
            <!--ANEXOS-->
            <br>
            @if($Anexos)
            <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($Anexos as $a)
                    <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">{{$a->Anexo}}</h5>
                      <p class="card-text">{{$a->DSAnexo}}</p>
                      <a href="{{url('storage/organizacao_' . Auth::user()->id_org . '_alunos/aluno_' . $a->CDPasta . '/' . $a->Anexo)}}" download class="btn btn-danger">Baixar</a>
                    </div>
                  </div>
                </div>
                  @endforeach
              </div>
              @endif
            <!--------->
        </div>
    </div>
</x-educacional-layout>