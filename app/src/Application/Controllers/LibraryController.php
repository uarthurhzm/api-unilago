<?php

namespace App\Application\Controllers;

use AlreadyReservedBookException;
use App\Domain\Library\DTO\GetAllCollectionsDTO;
use App\Domain\Library\DTO\GetLoanedBooksByStudentDTO;
use App\Domain\Library\Services\LibraryService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\Helpers\Validators;

class LibraryController extends ControllerBase
{
    private LibraryService $libraryService;

    public function __construct()
    {
        $this->libraryService = new LibraryService();
    }

    public function GetAllCollections(#[FromBody] GetAllCollectionsDTO $data): void
    {
        try {
            $collections = $this->libraryService->GetAllCollections($data);
            Response::success($collections, "Acervos recuperados com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar acervos: " . $th->getMessage());
        }
    }

    public function GetBookById(#[FromRoute] string $bookId): void
    {
        try {
            $book = $this->libraryService->GetBookById($bookId);
            Response::success($book, "Livro recuperado com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar livro: " . $th->getMessage());
        }
    }

    public function GetLoanedBooksByStudent(
        #[FromRoute] string $cd_mat,
        #[FromBody] GetLoanedBooksByStudentDTO $data
    ): void {
        try {
            $loanedBooks = $this->libraryService->GetLoanedBooksByStudent($cd_mat, $data->context);
            Response::success($loanedBooks, "Livros emprestados recuperados com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar livros emprestados: " . $th->getMessage());
        }
    }

    public function PostReserveBook(#[FromBody] GetAllCollectionsDTO $data): void
    {
        try {
            $this->libraryService->PostReserveBook($data);
            Response::success(null, "Reserva realizada com sucesso");
        } catch (AlreadyReservedBookException $th) {
            Response::forbidden($th->getMessage());
        } catch (\Throwable $th) {
            Response::error("Erro ao realizar reserva: " . $th->getMessage());
        }
    }

    public function GetReservedBooksByStudent(#[FromRoute] string $cd_mat): void
    {
        try {
            $reservedBooks = $this->libraryService->GetReservedBooksByStudent($cd_mat);
            Response::success($reservedBooks, "Livros reservados recuperados com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar livros reservados: " . $th->getMessage());
        }
    }

    public function CancelReserve(#[FromRoute] string $reserveId): void
    {
        try {
            $this->libraryService->CancelReserve($reserveId);
            Response::success(null, "Reserva cancelada com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao cancelar reserva: " . $th->getMessage());
        }
    }

    public function RenewBook(#[FromRoute] string $seq_epr): void
    {
        try {
            $this->libraryService->RenewBook($seq_epr);
            Response::success(null, "Empréstimo renovado com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao renovar empréstimo: " . $th->getMessage());
        }
    }
}
