package com.playarena.repository;

import com.playarena.entity.NXLTransaction;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface NXLTransactionRepository extends JpaRepository<NXLTransaction, Long> {
    List<NXLTransaction> findByUserIdOrderByDateDesc(Long userId);
}
