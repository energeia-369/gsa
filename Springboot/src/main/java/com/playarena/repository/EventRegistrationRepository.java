package com.playarena.repository;

import com.playarena.entity.EventRegistration;
import org.springframework.data.jpa.repository.JpaRepository;

public interface EventRegistrationRepository
        extends JpaRepository<EventRegistration, Long> {
}