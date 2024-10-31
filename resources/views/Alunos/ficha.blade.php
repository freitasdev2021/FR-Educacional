<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Save')}}" method="POST" enctype="multipart/form-data">
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
                @if(isset($Ficha->IDAluno) && isset($Ficha->IDMatricula))
                <input type="hidden" value="{{$Ficha->IDAluno}}" name="IDAluno">
                <input type="hidden" value="{{$Ficha->IDMatricula}}" name="IDMatricula">
                <input type="hidden" name="oldAnexoRG" value="{{$Ficha->AnexoRG}}">
                <input type="hidden" name="oldRGPaisAnexo" value="{{$Ficha->RGPaisAnexo}}">
                <input type="hidden" name="oldCResidencia" value="{{$Ficha->CResidencia}}">
                <input type="hidden" name="oldHistorico" value="{{$Ficha->Historico}}">
                <input type="hidden" name="oldFoto" value="{{$Ficha->Foto}}">
                <input type="hidden" name="CDPasta" value="{{$Ficha->CDPasta}}">
                @endif
                <div class="col-sm-12 p-2">
                    {{-- <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{!isset($Ficha->Foto) ? asset('img/kidAvatar.png') : url("storage/organizacao_".Auth::user()->id_org."alunos/aluno_$Ficha->CDPasta/$Ficha->Foto")}}"
                            style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome completo do Aluno</label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Ficha->Nome) ? $Ficha->Nome : ''}}" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Ficha->RG) ? $Ficha->RG : ''}}" name="RG" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Ficha->CPF) ? $Ficha->CPF : ''}}" name="CPF" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Ficha->Email) ? $Ficha->Email : ''}}" name="Email" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nascimento do Aluno</label>
                            <input type="date" class="form-control" value="{{isset($Ficha->Nascimento) ? $Ficha->Nascimento : ''}}" name="Nascimento" disabled> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                        <label>Nome completo do Responsavel</label>
                        <input type="text" class="form-control" value="{{isset($Ficha->NMResponsavel) ? $Ficha->NMResponsavel : ''}}" name="NMResponsavel" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Ficha->RGPais) ? $Ficha->RGPais : ''}}" name="RGPais" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>CPF do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Ficha->CPFResponsavel) ? $Ficha->CPFResponsavel : ''}}" name="CPFResponsavel" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>Email do Responsavel</label>
                            <input type="email" class="form-control" value="{{isset($Ficha->EmailResponsavel) ? $Ficha->EmailResponsavel : ''}}" name="EmailResponsavel" disabled> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" disabled value="{{isset($Ficha->CEP) ? $Ficha->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Ficha->Rua) ? $Ficha->Rua : ''}}" disabled>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Ficha->Bairro) ? $Ficha->Bairro : ''}}" minlength="2" disabled>
                        </div>
                        <div class="col-sm-1">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Ficha->UF) ? $Ficha->UF : ''}}" disabled>
                        </div>
                        <div class="col-sm-1">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Ficha->Numero) ? $Ficha->Numero : ''}}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Ficha->Cidade) ? $Ficha->Cidade : ''}}" minlength="3" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Responsavel</label>
                            <input type="text" name="CLResponsavel" class="form-control" maxlength="50" value="{{isset($Ficha->CLResponsavel) ? $Ficha->CLResponsavel : ''}}" minlength="3" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Celular" class="form-control" maxlength="50" value="{{isset($Ficha->Celular) ? $Ficha->Celular : ''}}" minlength="3" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Turma</label>
                            <input type="text" disabled class="form-control" value="">
                        </div>
                        <div class="col-sm-3">
                            <label>Possui NEE</label>
                            <select class="form-control" name="NEE" disabled>
                                <option value="1" {{isset($Ficha->NEE) && $Ficha->NEE == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Ficha->NEE) && $Ficha->NEE == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control" name="AMedico" disabled>
                                <option value="1" {{isset($Ficha->AMedico) && $Ficha->AMedico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Ficha->AMedico) && $Ficha->AMedico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control" name="APsicologico" disabled>
                                <option value="1" {{isset($Ficha->APsicologico) && $Ficha->APsicologico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Ficha->APsicologico) && $Ficha->APsicologico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Vencimento da Matrícula</label>
                            <input type="date" name="Vencimento" class="form-control" value="{{isset($Ficha->Vencimento) ? $Ficha->Vencimento : ''}}" disabled>
                        </div>
                        <div class="col-sm-3">
                            <label>Tem Alergia?</label>
                            <select class="form-control" name="Alergia" disabled>
                                <option value="1" {{isset($Ficha->Alergia) && $Ficha->Alergia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Ficha->Alergia) && $Ficha->Alergia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control" name="Transporte" disabled>
                                <option value="1" {{isset($Ficha->Transporte) && $Ficha->Transporte == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Ficha->Transporte) && $Ficha->Transporte == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control" name="BolsaFamilia" disabled>
                                <option value="1" {{isset($Ficha->BolsaFamilia) && $Ficha->BolsaFamilia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Ficha->BolsaFamilia) && $Ficha->BolsaFamilia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <br>
                            <a href="{{url("storage/organizacao_".$IDOrg."_alunos/aluno_$Ficha->CDPasta/$Ficha->RGPaisAnexo")}}" download class="btn btn-danger col-sm-12"><i class='bx bxs-file-pdf' ></i> RG dos Pais</a>
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <a href="{{url("storage/organizacao_".$IDOrg."_alunos/aluno_$Ficha->CDPasta/$Ficha->AnexoRG")}}" download class="btn btn-danger col-sm-12"><i class='bx bxs-file-pdf' ></i> RG do Aluno</a>
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <a href="{{url("storage/organizacao_".$IDOrg."_alunos/aluno_$Ficha->CDPasta/$Ficha->CResidencia")}}" download class="btn btn-danger col-sm-12"><i class='bx bxs-file-pdf' ></i> Comprovante de Residência</a>
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <a href="{{url("storage/organizacao_".$IDOrg."_alunos/aluno_$Ficha->CDPasta/$Ficha->Historico")}}" download class="btn btn-danger col-sm-12"><i class='bx bxs-file-pdf' ></i> Histórico Escolar</a>
                        </div>
                        <div class="col-sm-4">
                            <br>
                            <a href="{{url("storage/organizacao_".$IDOrg."_alunos/aluno_$Ficha->CDPasta/$Ficha->CNascimento")}}" download class="btn btn-danger col-sm-12"><i class='bx bxs-file-pdf' ></i> Certidão de Nascimento</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-educacional-layout>