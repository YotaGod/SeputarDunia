<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommentModel;

class CommentController extends BaseController
{
    public function index()
    {
        if (!has_permission('article-review')) {
            return redirect()->to(base_url('admin'))->with('error', 'Akses ditolak.');
        }

        $commentModel = new CommentModel();
        
        $db = \Config\Database::connect();
        $builder = $db->table('comments');
        $builder->select('comments.*, users.username, articles.title as article_title');
        $builder->join('users', 'users.id = comments.user_id');
        $builder->join('articles', 'articles.id = comments.article_id');
        $builder->orderBy('comments.created_at', 'DESC');
        
        $data = [
            'comments' => $builder->get()->getResultArray()
        ];

        return view('admin/comments/index', $data);
    }

    public function updateStatus($id)
    {
        if (!has_permission('article-review')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $status = $this->request->getPost('status');
        if (!in_array($status, ['approved', 'pending', 'rejected'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $commentModel = new CommentModel();
        $commentModel->update($id, ['status' => $status]);

        return redirect()->back()->with('success', 'Status komentar berhasil diubah menjadi ' . $status . '.');
    }

    public function delete($id)
    {
        if (!has_permission('article-review')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $commentModel = new CommentModel();
        $commentModel->delete($id);

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
