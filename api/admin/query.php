<?php
function survey_filters(array $input, array &$params): string {
    $where = [];
    $q = trim((string)($input['q'] ?? ''));
    $school = trim((string)($input['school'] ?? ''));
    $tester = trim((string)($input['tester'] ?? ''));
    $level = trim((string)($input['level'] ?? ''));
    $from = trim((string)($input['from'] ?? ''));
    $to = trim((string)($input['to'] ?? ''));

    if ($q !== '') {
        $where[] = '(full_name LIKE :q OR email LIKE :q OR phone LIKE :q OR school LIKE :q)';
        $params[':q'] = "%{$q}%";
    }
    if ($school !== '') { $where[] = 'school = :school'; $params[':school'] = $school; }
    if (in_array($tester, ['yes','maybe','no'], true)) { $where[] = 'willing_to_test = :tester'; $params[':tester'] = $tester; }
    if ($level !== '') { $where[] = 'student_level = :level'; $params[':level'] = $level; }
    if ($from !== '') { $where[] = 'DATE(created_at) >= :from'; $params[':from'] = $from; }
    if ($to !== '') { $where[] = 'DATE(created_at) <= :to'; $params[':to'] = $to; }
    return $where ? ' WHERE ' . implode(' AND ', $where) : '';
}
