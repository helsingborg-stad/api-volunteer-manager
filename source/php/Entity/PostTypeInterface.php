<?php

namespace VolunteerManager\Entity;

interface PostTypeInterface
{
    public function addHooks(): void;

    public function registerPostType(): void;

    public function addTableColumn(string $key, string $title, bool $sortable = false, callable|false $contentCallback = false): bool;

    public function setTableColumns(array $columns): array;

    public function tableSortableColumns(array $columns): array;

    public function tableColumnsContent(string $column, int $postId): void;
}
