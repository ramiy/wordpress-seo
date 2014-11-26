<?php

	abstract class WPSEO_Score {
		abstract public function score();
	}

	class WPSEO_Score_Body extends WPSEO_Score {
		
		protected $lengthScore

		/**
		 * @var string Scoring message shown when the body length is poor.
		 */
		protected $scoreBodyPoorLength = __( 'There are %d words contained in the body copy, this is below the %d word recommended minimum. Add more useful content on this topic for readers.', 'wordpress-seo' );

		/**
		 * @var string Scoring message shown when the body length is bad.
		 */
		protected $scoreBodyBadLength  = __( 'There are %d words contained in the body copy. This is far too low and should be increased.', 'wordpress-seo' );
		
		/**
		 * @var string Scoring message shown when the body length is ok.
		 */
		protected $scoreBodyOKLength   = __( 'There are %d words contained in the body copy, this is slightly below the %d word recommended minimum, add a bit more copy.', 'wordpress-seo' );
		
		/**
		 * @var string Scoring message shown when the body length is good.
		 */
		protected $scoreBodyGoodLength = __( 'There are %d words contained in the body copy, this is more than the %d word recommended minimum.', 'wordpress-seo' );
		
		/**
		 * @var string Scoring message shown when the keyword density is low.
		 */
		protected $scoreKeywordDensityLow  = __( 'The keyword density is %s%%, which is a bit low, the keyword was found %s times.', 'wordpress-seo' );
		
		/**
		 * @var string Scoring message shown when the keyword density is high.
		 */
		protected $scoreKeywordDensityHigh = __( 'The keyword density is %s%%, which is over the advised 4.5%% maximum, the keyword was found %s times.', 'wordpress-seo' );
		
		/**
		 * @var string Scoring message shown when the keyword density is good.
		 */
		protected $scoreKeywordDensityGood = __( 'The keyword density is %s%%, which is great, the keyword was found %s times.', 'wordpress-seo' );

		/**
		 * @var string Scoring message shown when the keyword doesn't appear in the first paragraph.
		 */
		protected $scoreFirstParagraphLow  = __( 'The keyword doesn\'t appear in the first paragraph of the copy, make sure the topic is clear immediately.', 'wordpress-seo' );
		
		/**
		 * @var string Scoring message shown when the keyword appears in the first paragraph.
		 */
		protected $scoreFirstParagraphHigh = __( 'The keyword appears in the first paragraph of the copy.', 'wordpress-seo' );

		/**
		 * @var string The link to the Wikipedia section about the Flesch reading ease test.
		 */
		protected $fleschurl   = '<a href="http://en.wikipedia.org/wiki/Flesch-Kincaid_readability_test#Flesch_Reading_Ease">' . __( 'Flesch Reading Ease', 'wordpress-seo' ) . '</a>';
		
		/**
		 * @var string The message containing the Flesch score.
		 */
		protected $scoreFlesch = __( 'The copy scores %s in the %s test, which is considered %s to read. %s', 'wordpress-seo' );

		protected $keyword_density = 0;


		public function __construct( $job, &$results, $body, $first_paragraph ) {
			$this->job = sanitize_job( $job );
			$this->results = $results;
			$this->body = $this->sanitize_body( $body );
			$this->first_paragraph = sanitize_first_paragraph( $first_paragraph );

			$this->length_score = filter_length_score();

			$this->word_count = $this->statistics()->word_count( $this->body );
			$this->keyword_word_count = $this->statistics()->word_count( $this->job['keyword'] );
		}

		public function score() {
			$this->save_body_score();
			$this->save_keyword_density_score();
			$this->save_first_paragraph_score();
			$this->save_flesch_score();
		}

		private function filter_length_score() {
			$length_score = array(
				'good' => 300,
				'ok'   => 250,
				'poor' => 200,
				'bad'  => 100,
			);
			$length_score = apply_filters( 'wpseo_body_length_score', $length_score, $job );
			
			return $length_score;
		}

		private function sanitize_job( $job ) {
			$job['keyword'] = $this->strtolower_utf8( $job['keyword'] );

			return $job;
		}

		private function sanitize_body( $body ) {
			$body = preg_replace( '`<img(?:[^>]+)?alt="([^"]+)"(?:[^>]+)>`', '$1', $body );
			$body = strip_tags( $body );
			$body = $this->strtolower_utf8( $body );

			return $body;
		}

		private function sanitize_first_paragraph( $first_paragraph ) {
			$first_paragraph = $this->strtolower_utf8( $first_paragraph );

			return $first_paragraph
		}

		private function save_body_score() {
			if ( $this->wordCount < $lengthScore['bad'] ) {
				$this->save_score_result( $this->results, - 20, sprintf( $this->scoreBodyBadLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
			} elseif ( $wordCount < $lengthScore['poor'] ) {
				$this->save_score_result( $this->results, - 10, sprintf( $this->scoreBodyPoorLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
			} elseif ( $wordCount < $lengthScore['ok'] ) {
				$this->save_score_result( $this->results, 5, sprintf( $this->scoreBodyPoorLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
			} elseif ( $wordCount < $lengthScore['good'] ) {
				$this->save_score_result( $this->results, 7, sprintf( $this->scoreBodyOKLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
			} else {
				$this->save_score_result( $this->results, 9, sprintf( $this->scoreBodyGoodLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
			}
		}

		private function save_keyword_density_score() {
			if ( $keywordWordCount > 10 ) {
				$this->save_score_result( $this->results, 0, __( 'Your keyphrase is over 10 words, a keyphrase should be shorter and there can be only one keyphrase.', 'wordpress-seo' ), 'focus_keyword_length' );
			} else {
			// Keyword Density check
			if ( $wordCount > 100 ) {
				$this->calculate_keyword_density();
				
				if ( $this->keyword_density < 1 ) {
					$this->save_score_result( $this->results, 4, sprintf( $sthis->coreKeywordDensityLow, $keywordDensity, $keywordCount ), 'keyword_density' );
				} 
				elseif ( $keyword_density > 4.5 ) {
					$this->save_score_result( $this->results, - 50, sprintf( $this->scoreKeywordDensityHigh, $keywordDensity, $keywordCount ), 'keyword_density' );
				} 
				else {
					$this->save_score_result( $this->results, 9, sprintf( $this->scoreKeywordDensityGood, $keywordDensity, $keywordCount ), 'keyword_density' );
				}
			}
		}

		private function calculate_keyword_density() {
			$keywordCount = preg_match_all( '`\b' . preg_quote( $this->job['keyword'], '`' ) . '\b`miu', $this->body, $res );
				if ( ( $keywordCount > 0 && $this->keyword_word_count > 0 ) && $this->word_count > $keywordCount ) {
					$this->keywordDensity = wpseo_calc( 
						wpseo_calc( 
							$keywordCount, 
							'/', 
							wpseo_calc( 
								$this->word_count, 
								'-', 
								( wpseo_calc( 
									wpseo_calc( 
										$this->keyword_word_count, 
										'-', 
										1 
									), 
									'*', 
									$keywordCount 
								) 
							) 
						) 
					), 
					'*', 
					100, 
					true, 
					2 
				);
			}
		}

		private function save_first_paragraph_score() {
			// First Paragraph Test
			// check without /u modifier as well as /u might break with non UTF-8 chars.
			if ( 
				preg_match( 
					'`\b' . preg_quote( 
						$this->job['keyword'], 
						'`' 
					) . '\b`miu', 
					$this->first_paragraph 
				) || preg_match( 
					'`\b' . preg_quote( 
						$this->job['keyword'], 
						'`' 
					) . '\b`mi', 
					$this->first_paragraph 
				) || preg_match( 
					'`\b' . preg_quote( 
						$this->job['keyword_folded'], 
						'`' 
					) . '\b`miu', 
					$this->first_paragraph 
				)
			) {
				$this->save_score_result( $this->results, 9, $this->scoreFirstParagraphHigh, 'keyword_first_paragraph' );
			} 
			else {
				$this->save_score_result( $this->results, 3, $this->scoreFirstParagraphLow, 'keyword_first_paragraph' );
			}
		}

		private function save_flesch_score() {
			$lang = get_bloginfo( 'language' );
			if ( substr( $lang, 0, 2 ) == 'en' && $wordCount > 100 ) {
				// Flesch Reading Ease check
				$flesch = $this->statistics()->flesch_kincaid_reading_ease( $body );

				$note  = '';
				$level = '';
				$score = 1;
				if ( $flesch >= 90 ) {
					$level = __( 'very easy', 'wordpress-seo' );
					$score = 9;
				} elseif ( $flesch >= 80 ) {
					$level = __( 'easy', 'wordpress-seo' );
					$score = 9;
				} elseif ( $flesch >= 70 ) {
					$level = __( 'fairly easy', 'wordpress-seo' );
					$score = 8;
				} elseif ( $flesch >= 60 ) {
					$level = __( 'OK', 'wordpress-seo' );
					$score = 7;
				} elseif ( $flesch >= 50 ) {
					$level = __( 'fairly difficult', 'wordpress-seo' );
					$note  = __( 'Try to make shorter sentences to improve readability.', 'wordpress-seo' );
					$score = 6;
				} elseif ( $flesch >= 30 ) {
					$level = __( 'difficult', 'wordpress-seo' );
					$note  = __( 'Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
					$score = 5;
				} elseif ( $flesch >= 0 ) {
					$level = __( 'very difficult', 'wordpress-seo' );
					$note  = __( 'Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
					$score = 4;
				}
				$this->save_score_result( $results, $score, sprintf( $scoreFlesch, $flesch, $fleschurl, $level, $note ), 'flesch_kincaid' );
			}
		}
	}