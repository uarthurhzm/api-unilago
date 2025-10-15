<?php

namespace App\Domain\Library\Services;

use AlreadyReservedBookException;
use App\Domain\Library\DTO\GetAllCollectionsDTO;
use App\Domain\Library\Repositories\LibraryRepository;

class LibraryService
{
    public function __construct(private LibraryRepository $libraryRepository) {}

    public function GetAllCollections(GetAllCollectionsDTO $data)
    {
        $searchOption = $data->searchOption;

        $fields = explode('-', $searchOption);
        $field = '';
        $execute = [];

        if (count($fields) === 1 && $fields[0] !== 'all') {
            $field = 'AND acervo.' . $fields[0] . ' like ?';
            $execute = ['%' . $data->searchQuery . '%'];
        } else {
            $conditions = [];

            foreach ($fields as $fieldName) {
                if ($fieldName === 'all') {
                    $field = 'AND (acervo.DESC_ASSUNTO like ? OR acervo.NM_ACV like ? OR acervo.NM_AUTOR1 like ?)';
                    $execute = ['%' . $data->searchQuery . '%', '%' . $data->searchQuery . '%', '%' . $data->searchQuery . '%'];
                    break;
                } else {
                    $conditions[] = 'acervo.' . $fieldName . ' like ?';
                    $execute[] = '%' . $data->searchQuery . '%';
                }
            }

            if (!empty($conditions)) {
                $field = 'AND (' . implode(' OR ', $conditions) . ')';
            }
        }

        return array_merge($this->libraryRepository->GetAllCollections($data, $field, $execute), $this->libraryRepository->GetAllDigitalCollections($data));
    }

    public function GetBookById($bookId)
    {
        return $this->libraryRepository->GetBookById($bookId);
    }

    public function GetLoanedBooksByStudent($cd_mat, $context)
    {
        $status = $context === 'now' ? "1, 3" : "2";
        return $this->libraryRepository->GetLoanedBooksByStudent($cd_mat, $status);
    }

    public function PostReserveBook(GetAllCollectionsDTO $data)
    {
        if ($this->libraryRepository->CheckReserve($data))
            throw new AlreadyReservedBookException();

        return $this->libraryRepository->PostReserveBook($data);
    }

    public function GetReservedBooksByStudent($cd_mat)
    {
        return $this->libraryRepository->GetReservedBooksByStudent($cd_mat);
    }

    public function CancelReserve($reserveId)
    {
        return $this->libraryRepository->CancelReserve($reserveId);
    }

    public function RenewBook($seq_epr)
    {
        $this->libraryRepository->RenewBook($seq_epr);
    }
}
