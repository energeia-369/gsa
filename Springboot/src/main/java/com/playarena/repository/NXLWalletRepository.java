package com.playarena.repository;

import com.playarena.entity.NXLWallet;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.Optional;

@Repository
public interface NXLWalletRepository extends JpaRepository<NXLWallet, Long> {
    Optional<NXLWallet> findByUserId(Long userId);
}
