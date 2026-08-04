<?php

namespace App\Models;

use CodeIgniter\Model;

class PredictionQueryModel extends Model
{
    protected $table = 'prediction_queries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'name',
        'query_text',
        'criteria',
        'technical_criteria',
        'match_mode',
        'method',
        'horizon_days',
        'last_run_at',
        'status',
        'is_public',
    ];

    public function getById(int $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getPublic(): array
    {
        $query = $this->db->table('prediction_queries');
        $query->select([
            'prediction_queries.*',
            'users.name as creator_name',
            'COUNT(prediction_query_results.id) as results_count',
            'AVG(prediction_query_results.confidence_score) as avg_confidence',
        ])
            ->join('users', 'users.id = prediction_queries.user_id', 'left')
            ->join('prediction_query_results', 'prediction_query_results.query_id = prediction_queries.id', 'left')
            ->where('prediction_queries.is_public', 1)
            ->groupBy('prediction_queries.id')
            ->orderBy('prediction_queries.created_at', 'DESC');

        return $query->get()->getResultArray();
    }

    public function getAllForAdmin(): array
    {
        $query = $this->db->table('prediction_queries');
        $query->select([
            'prediction_queries.*',
            'users.name as creator_name',
            'users.email as creator_email',
            'COUNT(prediction_query_results.id) as results_count',
            'AVG(prediction_query_results.confidence_score) as avg_confidence',
        ])
            ->join('users', 'users.id = prediction_queries.user_id', 'left')
            ->join('prediction_query_results', 'prediction_query_results.query_id = prediction_queries.id', 'left')
            ->groupBy('prediction_queries.id')
            ->orderBy('prediction_queries.created_at', 'DESC');

        return $query->get()->getResultArray();
    }

    public function togglePublic(int $id): bool
    {
        $current = $this->where('id', $id)->first();
        if (!$current) {
            return false;
        }

        $newValue = $current['is_public'] ? 0 : 1;

        return $this->update($id, ['is_public' => $newValue]);
    }

    public function getWithResults(int $id): ?array
    {
        $query = $this->db->table('prediction_queries');
        $query->select([
            'prediction_queries.*',
            'COUNT(prediction_query_results.id) as results_count',
            'AVG(prediction_query_results.confidence_score) as avg_confidence',
        ])
            ->join('prediction_query_results', 'prediction_query_results.query_id = prediction_queries.id', 'left')
            ->where('prediction_queries.id', $id)
            ->groupBy('prediction_queries.id');

        return $query->get()->getRowArray() ?: null;
    }

    public function getUserWithResults(int $userId): array
    {
        $query = $this->db->table('prediction_queries');
        $query->select([
            'prediction_queries.*',
            'COUNT(prediction_query_results.id) as results_count',
            'AVG(prediction_query_results.confidence_score) as avg_confidence',
        ])
            ->join('prediction_query_results', 'prediction_query_results.query_id = prediction_queries.id', 'left')
            ->where('prediction_queries.user_id', $userId)
            ->groupBy('prediction_queries.id');

        return $query->get()->getResultArray();
    }
}